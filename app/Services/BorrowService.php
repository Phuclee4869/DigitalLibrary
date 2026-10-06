<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Repositories\BookRepository;
use App\Repositories\BorrowRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BorrowService
{
    public const MAX_TITLES_PER_TICKET = 5;
    public const MAX_QTY_PER_TITLE     = 3;
    public const MAX_BOOKS_PER_READER  = 5;
    public const LOAN_DAYS             = 14;
    public const FINE_PER_BOOK_PER_DAY = 5000;

    public function __construct(
        protected BookRepository $books,
        protected BorrowRepository $borrows
    ) {}

    /**
     * LUỒNG 1: Lập phiếu mượn (Bọc trong DB::transaction + Pessimistic Lock)
     */
    public function createBorrowTicket(int $userId, array $data): array
    {
        return DB::transaction(function () use ($userId, $data) {
            // QT1: Độc giả tồn tại
            $reader = $this->borrows->findReaderForUpdate((int)$data['doc_gia_id']);
            if (!$reader) {
                throw new BusinessException('Mã độc giả không tồn tại trong hệ thống.', 'READER_NOT_FOUND', 404);
            }

            $items = collect($data['sach'])->map(fn ($i) => [
                'sach_id'  => (int)$i['sach_id'],
                'so_luong' => (int)$i['so_luong'],
            ]);

            // QT2: Đang mượn + mượn mới <= 5 cuốn
            $borrowing = $this->borrows->countBooksBorrowing($reader->id);
            $requested = (int)$items->sum('so_luong');
            if (($borrowing + $requested) > self::MAX_BOOKS_PER_READER) {
                throw new BusinessException(
                    "Độc giả đang mượn {$borrowing} cuốn, mượn thêm {$requested} cuốn sẽ vượt giới hạn " . self::MAX_BOOKS_PER_READER . " cuốn.",
                    'BORROW_LIMIT_EXCEEDED',
                    422,
                    ['dang_muon' => $borrowing, 'muon_them' => $requested, 'gioi_han' => self::MAX_BOOKS_PER_READER]
                );
            }

            // Sắp xếp ID tăng dần để khóa theo thứ tự chống Deadlock (QT13)
            $sortedIds = $items->pluck('sach_id')->sort()->values()->all();
            $booksMap = $this->books->getBooksForUpdate($sortedIds);

            $now = now();
            $due = $now->copy()->addDays(self::LOAN_DAYS);
            $ticketId = $this->borrows->createTicket($reader->id, $userId, $now, $due);

            $lines = [];
            foreach ($items as $item) {
                $book = $booksMap->get($item['sach_id']);

                // QT3: Sách phải tồn tại
                if (!$book) {
                    throw new BusinessException("Sách có mã ID {$item['sach_id']} không tồn tại.", 'BOOK_NOT_FOUND', 404, ['sach_id' => $item['sach_id']]);
                }

                // QT4: Kho phải đủ số lượng
                if ($book->so_luong_con_lai < $item['so_luong']) {
                    throw new BusinessException(
                        "Sách "{$book->ten_sach}" (ID {$book->id}) chỉ còn {$book->so_luong_con_lai} cuốn, không đủ {$item['so_luong']} cuốn.",
                        'OUT_OF_STOCK',
                        409,
                        ['sach_id' => $book->id, 'con_lai' => (int)$book->so_luong_con_lai, 'yeu_cau' => $item['so_luong']]
                    );
                }

                $this->borrows->addTicketItem($ticketId, $item['sach_id'], $item['so_luong']);

                // Trừ kho nguyên tố
                if (!$this->books->decreaseStock($item['sach_id'], $item['so_luong'])) {
                    throw new BusinessException("Không thể cập nhật kho cho sách ID {$item['sach_id']}.", 'OUT_OF_STOCK', 409, ['sach_id' => $item['sach_id']]);
                }

                $lines[] = [
                    'sach_id'  => $item['sach_id'],
                    'ten_sach' => $book->ten_sach,
                    'so_luong' => $item['so_luong'],
                ];
            }

            return [
                'phieu_muon_id' => $ticketId,
                'doc_gia_id'    => $reader->id,
                'ngay_muon'     => $now->toDateTimeString(),
                'han_tra'       => $due->toDateTimeString(),
                'trang_thai'    => BorrowRepository::STATUS_BORROWING,
                'sach'          => $lines,
            ];
        });
    }

    /**
     * LUỒNG 2: Trả sách và tính phạt
     */
    public function returnBorrowTicket(int $ticketId): array
    {
        return DB::transaction(function () use ($ticketId) {
            // QT5: Phiếu mượn phải tồn tại
            $ticket = $this->borrows->findTicketForUpdate($ticketId);
            if (!$ticket) {
                throw new BusinessException('Phiếu mượn không tồn tại.', 'TICKET_NOT_FOUND', 404);
            }

            // QT6: Chỉ trả phiếu đang mượn
            if ($ticket->trang_thai !== BorrowRepository::STATUS_BORROWING) {
                throw new BusinessException('Phiếu mượn này đã được trả trước đó.', 'TICKET_ALREADY_RETURNED', 409, ['trang_thai' => $ticket->trang_thai]);
            }

            $returnedAt = now();
            $lateDays = $this->calculateLateDays($ticket->han_tra, $returnedAt);

            $lines = [];
            $totalFine = 0.0;

            foreach ($this->borrows->getTicketItems($ticketId) as $item) {
                // QT7: Tiền phạt = trễ ngày * số cuốn * đơn giá
                $fine = (float) ($lateDays * $item->so_luong * self::FINE_PER_BOOK_PER_DAY);
                $totalFine += $fine;

                $this->borrows->updateItemFine($item->id, $fine);
                $this->books->increaseStock((int)$item->sach_id, (int)$item->so_luong);

                $lines[] = [
                    'sach_id'  => (int)$item->sach_id,
                    'ten_sach' => $item->ten_sach,
                    'so_luong' => (int)$item->so_luong,
                    'tien_pat' => $fine,
                ];
            }

            $this->borrows->markReturned($ticketId, $returnedAt);

            return [
                'phieu_muon_id'  => $ticketId,
                'trang_thai'     => BorrowRepository::STATUS_RETURNED,
                'ngay_tra'       => $returnedAt->toDateTimeString(),
                'so_ngay_tre'    => $lateDays,
                'tong_tien_phat' => $totalFine,
                'sach'           => $lines,
            ];
        });
    }

    /**
     * LUỒNG 3: Gia hạn phiếu mượn (Bổ sung Buổi 6)
     */
    public function renewBorrowTicket(int $ticketId): array
    {
        return DB::transaction(function () use ($ticketId) {
            // 1. Khóa dòng phiếu mượn
            $ticket = $this->borrows->findTicketForUpdate($ticketId);

            if (!$ticket) {
                throw new BusinessException('Không tìm thấy phiếu mượn.', 'TICKET_NOT_FOUND', 404);
            }

            // QT9: Kiểm tra trạng thái "đang mượn"
            if ($ticket->trang_thai !== BorrowRepository::STATUS_BORROWING) {
                throw new BusinessException('Chỉ phiếu đang mượn mới được gia hạn.', 'TICKET_NOT_BORROWING', 409);
            }

            // QT11: Kiểm tra giới hạn gia hạn (Tối đa 1 lần)
            if (($ticket->so_lan_gia_han ?? 0) >= 1) {
                throw new BusinessException('Phiếu mượn này đã được gia hạn tối đa 1 lần.', 'RENEW_LIMIT_EXCEEDED', 409);
            }

            // QT10: Kiểm tra quá hạn
            $dueDate = Carbon::parse($ticket->han_tra);
            if (now()->greaterThan($dueDate)) {
                throw new BusinessException('Phiếu mượn đã quá hạn, không thể gia hạn. Vui lòng trả sách và nộp phạt.', 'TICKET_OVERDUE', 422);
            }

            // QT12: Cộng thêm 14 ngày vào hạn trả
            $newDueDate = $dueDate->addDays(self::LOAN_DAYS)->toDateTimeString();
            $this->borrows->renewTicket($ticketId, $newDueDate);

            return [
                'phieu_muon_id'  => $ticketId,
                'han_tra_cu'     => $ticket->han_tra,
                'han_tra_moi'    => $newDueDate,
                'so_lan_gia_han' => ($ticket->so_lan_gia_han ?? 0) + 1,
                'message'        => 'Gia hạn phiếu mượn thành công thêm 14 ngày.'
            ];
        });
    }

    protected function calculateLateDays($dueAt, Carbon $returnedAt): int
    {
        if (!$dueAt) return 0;
        $due = Carbon::parse($dueAt);
        if ($returnedAt->timestamp <= $due->timestamp) return 0;
        return (int) ceil(($returnedAt->timestamp - $due->timestamp) / 86400);
    }
}
