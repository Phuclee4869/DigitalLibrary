<?php

namespace App\Services;

use App\Repositories\BookRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class BookService
{
    protected BookRepository $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    /**
     * Lấy danh sách sách
     */
    public function getBooksList()
    {
        return $this->bookRepository->getBooks(10);
    }

    /**
     * Xử lý mượn sách
     */
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
                'trang_thai' => 'đang mượn',
            ]);

            foreach ($data['sach'] as $item) {

                $book = $this->bookRepository->findBookById(
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
     * Kiểm tra quyền truy cập phiếu mượn
     */
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
