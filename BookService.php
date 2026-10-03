<?php

namespace App\Services;

use App\Repositories\BookRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class BookService
{
    // Hằng số nghiệp vụ của luồng trả sách
    public const SO_NGAY_MUON_TOI_DA = 14;   // số ngày được mượn
    public const TIEN_PHAT_MOI_NGAY  = 5000; // VND / cuốn / ngày trễ

    protected BookRepository $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    public function getBooksList()
    {
        return $this->bookRepository->getBooks(10);
    }

    public function createBorrowTicket($userId, $data)
    {
        $docGia = DB::table('doc_gia')
            ->where('id', $data['doc_gia_id'])
            ->first();

        if (!$docGia) {
            throw new Exception(
                'Mã độc giả không tồn tại trong hệ thống.'
            );
        }

        return DB::transaction(function () use ($userId, $data) {

            $ticketId = DB::table('phieu_muon')->insertGetId([
                'doc_gia_id' => $data['doc_gia_id'],
                'user_id' => $userId,
                'ngay_muon' => now(),
                'han_tra' => now()->addDays(self::SO_NGAY_MUON_TOI_DA),
                'trang_thai' => 'đang mượn',
            ]);

            foreach ($data['sach'] as $item) {

                // Khóa hàng sách để các yêu cầu mượn đồng thời phải xếp hàng
                $book = $this->bookRepository->findBookForUpdate(
                    $item['sach_id']
                );

                if (
                    !$book ||
                    $book->so_luong_con_lai < $item['so_luong']
                ) {
                    throw new Exception(
                        "Sách có mã ID {$item['sach_id']} " .
                        "đã hết hoặc không đủ số lượng trong kho."
                    );
                }

                DB::table('chi_tiet_phieu_muon')->insert([
                    'phieu_muon_id' => $ticketId,
                    'sach_id' => $item['sach_id'],
                    'so_luong' => $item['so_luong'],
                    'tien_pat' => 0.00,
                ]);

                $updated = $this->bookRepository->updateQuantity(
                    $item['sach_id'],
                    -intval($item['so_luong'])
                );

                if (!$updated) {
                    throw new Exception(
                        "Không thể cập nhật số lượng sách ID " .
                        $item['sach_id'] . "."
                    );
                }
            }

            return $ticketId;
        });
    }

    /**
     * Tính số ngày trễ hạn (số nguyên, >= 0).
     * Trả đúng hạn hoặc sớm hơn thì trễ = 0.
     */
    public function calculateOverdueDays($hanTra, $ngayTra): int
    {
        $han = Carbon::parse($hanTra)->startOfDay();
        $tra = Carbon::parse($ngayTra)->startOfDay();

        if ($tra->lte($han)) {
            return 0;
        }

        return $han->diffInDays($tra);
    }

    /**
     * LUỒNG 3: Trả sách và tính tiền phạt trễ hạn.
     * Thuật toán: ReturnBooksWithLateFee (xem mã giả ở báo cáo mục 3.6).
     */
    public function returnBorrowTicket($ticketId)
    {
        return DB::transaction(function () use ($ticketId) {

            // B1: khóa dòng phiếu mượn, chặn 2 yêu cầu trả cùng lúc
            $ticket = DB::table('phieu_muon')
                ->where('id', $ticketId)
                ->lockForUpdate()
                ->first();

            // B2: kiểm tra tồn tại và trạng thái
            if (!$ticket) {
                throw new Exception('Phiếu mượn không tồn tại.', 404);
            }

            if ($ticket->trang_thai === 'đã trả') {
                throw new Exception('Phiếu mượn này đã được trả trước đó.', 409);
            }

            // B3: tính số ngày trễ
            $ngayTra = now();
            $soNgayTre = $this->calculateOverdueDays($ticket->han_tra, $ngayTra);

            // B4: duyệt từng dòng chi tiết, tính phạt và hoàn kho
            $details = DB::table('chi_tiet_phieu_muon')
                ->where('phieu_muon_id', $ticketId)
                ->get();

            $tongPhat = 0;

            foreach ($details as $d) {
                $tienPhat = $soNgayTre * $d->so_luong * self::TIEN_PHAT_MOI_NGAY;

                DB::table('chi_tiet_phieu_muon')
                    ->where('id', $d->id)
                    ->update(['tien_pat' => $tienPhat]);

                $this->bookRepository->updateQuantity(
                    $d->sach_id,
                    intval($d->so_luong)
                );

                $tongPhat += $tienPhat;
            }

            // B5: đóng phiếu
            DB::table('phieu_muon')
                ->where('id', $ticketId)
                ->update([
                    'ngay_tra'   => $ngayTra,
                    'trang_thai' => 'đã trả',
                ]);

            return [
                'phieu_muon_id'  => (int) $ticketId,
                'so_ngay_tre'    => $soNgayTre,
                'tong_tien_phat' => $tongPhat,
            ];
        });
    }

    public function checkObjectOwnership(
        $ticketId,
        $userId,
        $userRole
    ) {
        if ($userRole == 1 || $userRole == 2) {
            return true;
        }

        $ticket = DB::table('phieu_muon')
            ->where('id', $ticketId)
            ->first();

        if (!$ticket || $ticket->user_id != $userId) {
            abort(
                403,
                'Bạn không có quyền truy cập hoặc chỉnh sửa phiếu mượn này.'
            );
        }

        return true;
    }
}