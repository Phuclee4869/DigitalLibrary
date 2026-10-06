<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Repositories\BookRepository;
use App\Repositories\BorrowRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Tầng nghiệp vụ: hai luồng cốt lõi của hệ thống thư viện số
 *   1. Lập phiếu mượn sách
 *   2. Trả sách và tính tiền phạt
 * Mọi quy tắc nghiệp vụ và ranh giới transaction nằm ở đây.
 */
class BorrowService
{
    // ---- Tham số quy tắc nghiệp vụ (có thể chỉnh theo yêu cầu nhóm) ----
    public const MAX_TITLES_PER_TICKET = 5;     // tối đa 5 đầu sách / phiếu
    public const MAX_QTY_PER_TITLE     = 3;     // tối đa 3 cuốn / đầu sách
    public const MAX_BOOKS_PER_READER  = 5;     // tối đa 5 cuốn đang mượn cùng lúc / độc giả
    public const LOAN_DAYS             = 14;    // thời hạn mượn (ngày)
    public const FINE_PER_BOOK_PER_DAY = 5000;  // phạt (đồng) / cuốn / ngày trễ

    public function __construct(
        protected BookRepository $books,
        protected BorrowRepository $borrows
    ) {
    }

    /**
     * LUỒNG 1: Lập phiếu mượn.
     * Toàn bộ thao tác ghi (phiếu, chi tiết, trừ kho) nằm trong MỘT transaction:
     * bất kỳ bước nào ném BusinessException đều rollback tất cả.
     *
     * @param  int   $userId  Người lập phiếu (admin/thủ thư)
     * @param  array $data    ['doc_gia_id' => int, 'sach' => [['sach_id' => int, 'so_luong' => int]]]
     */
    public function createBorrowTicket(int $userId, array $data): array
    {
        return DB::transaction(function () use ($userId, $data) {

            // QT1: độc giả phải tồn tại (khóa dòng để các phiếu của cùng độc giả chạy tuần tự)
            $reader = $this->borrows->findReaderForUpdate((int) $data['doc_gia_id']);
            if (!$reader) {
                throw new BusinessException(
                    'Mã độc giả không tồn tại trong hệ thống.',
                    'READER_NOT_FOUND',
                    404
                );
            }

            $items = collect($data['sach'])->map(fn ($i) => [
                'sach_id'  => (int) $i['sach_id'],
                'so_luong' => (int) $i['so_luong'],
            ]);

            // QT2: tổng số cuốn đang mượn + mượn mới không vượt giới hạn
            $borrowing = $this->borrows->countBooksBorrowing($reader->id);
            $requested = (int) $items->sum('so_luong');
            if ($borrowing + $requested > self::MAX_BOOKS_PER_READER) {
                throw new BusinessException(
                    'Độc giả đang mượn ' . $borrowing . ' cuốn, mượn thêm ' . $requested
                    . ' cuốn sẽ vượt giới hạn ' . self::MAX_BOOKS_PER_READER . ' cuốn.',
                    'BORROW_LIMIT_EXCEEDED',
                    422,
                    [
                        'dang_muon' => $borrowing,
                        'muon_them' => $requested,
                        'gioi_han'  => self::MAX_BOOKS_PER_READER,
                    ]
                );
            }

            // Khóa các dòng sách sẽ mượn để không ai trừ kho chen ngang
            $books = $this->books->getBooksForUpdate($items->pluck('sach_id')->all());

            $now = now();
            $due = $now->copy()->addDays(self::LOAN_DAYS);
            $ticketId = $this->borrows->createTicket($reader->id, $userId, $now, $due);

            $lines = [];
            foreach ($items as $item) {
                $book = $books->get($item['sach_id']);

                // QT3: sách phải tồn tại
                if (!$book) {
                    throw new BusinessException(
                        "Sách có mã ID {$item['sach_id']} không tồn tại.",
                        'BOOK_NOT_FOUND',
                        404,
                        ['sach_id' => $item['sach_id']]
                    );
                }

                // QT4: kho phải còn đủ số lượng
                if ($book->so_luong_con_lai < $item['so_luong']) {
                    throw new BusinessException(
                        "Sách \"{$book->ten_sach}\" (ID {$book->id}) chỉ còn "
                        . "{$book->so_luong_con_lai} cuốn, không đủ {$item['so_luong']} cuốn.",
                        'OUT_OF_STOCK',
                        409,
                        [
                            'sach_id' => $book->id,
                            'con_lai' => (int) $book->so_luong_con_lai,
                            'yeu_cau' => $item['so_luong'],
                        ]
                    );
                }

                $this->borrows->addTicketItem($ticketId, $item['sach_id'], $item['so_luong']);

                // Trừ kho có điều kiện (so_luong_con_lai >= số lượng): lớp bảo vệ thứ hai
                if (!$this->books->decreaseStock($item['sach_id'], $item['so_luong'])) {
                    throw new BusinessException(
                        "Không thể cập nhật kho cho sách ID {$item['sach_id']}.",
                        'OUT_OF_STOCK',
                        409,
                        ['sach_id' => $item['sach_id']]
                    );
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
     * LUỒNG 2: Trả sách (trả toàn bộ phiếu) và tính tiền phạt trễ hạn.
     */
    public function returnBorrowTicket(int $ticketId): array
    {
        return DB::transaction(function () use ($ticketId) {

            // QT5: phiếu phải tồn tại (khóa dòng để tránh hai người cùng bấm "trả")
            $ticket = $this->borrows->findTicketForUpdate($ticketId);
            if (!$ticket) {
                throw new BusinessException(
                    'Phiếu mượn không tồn tại.',
                    'TICKET_NOT_FOUND',
                    404
                );
            }

            // QT6: chỉ trả phiếu đang ở trạng thái "đang mượn"
            if ($ticket->trang_thai !== BorrowRepository::STATUS_BORROWING) {
                throw new BusinessException(
                    'Phiếu mượn này đã được trả trước đó.',
                    'TICKET_ALREADY_RETURNED',
                    409,
                    ['trang_thai' => $ticket->trang_thai]
                );
            }

            $returnedAt = now();
            $lateDays   = $this->calculateLateDays($ticket->han_tra, $returnedAt);

            $lines     = [];
            $totalFine = 0.0;

            foreach ($this->borrows->getTicketItems($ticketId) as $item) {
                // QT7: tiền phạt = số ngày trễ x số cuốn x đơn giá
                $fine = (float) ($lateDays * $item->so_luong * self::FINE_PER_BOOK_PER_DAY);

                $this->borrows->updateItemFine($item->id, $fine);
                $this->books->increaseStock((int) $item->sach_id, (int) $item->so_luong);

                $totalFine += $fine;
                $lines[] = [
                    'sach_id'  => (int) $item->sach_id,
                    'ten_sach' => $item->ten_sach,
                    'so_luong' => (int) $item->so_luong,
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
     * Số ngày trễ, làm tròn lên (trễ 1 giờ vẫn tính 1 ngày). Trả đúng hạn = 0.
     */
    protected function calculateLateDays($dueAt, Carbon $returnedAt): int
    {
        if (!$dueAt) {
            return 0;
        }

        $due = Carbon::parse($dueAt);
        if ($returnedAt->timestamp <= $due->timestamp) {
            return 0;
        }

        return (int) ceil(($returnedAt->timestamp - $due->timestamp) / 86400);
    }
}
