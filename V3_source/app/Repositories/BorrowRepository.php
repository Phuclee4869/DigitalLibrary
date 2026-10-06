<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Tầng dữ liệu: truy vấn doc_gia, phieu_muon, chi_tiet_phieu_muon.
 */
class BorrowRepository
{
    public const STATUS_BORROWING = 'đang mượn';
    public const STATUS_RETURNED  = 'đã trả';

    /**
     * Khóa dòng độc giả để hai phiếu mượn của cùng một người không chạy song song.
     * Phải gọi bên trong DB::transaction.
     */
    public function findReaderForUpdate(int $id)
    {
        return DB::table('doc_gia')->where('id', $id)->lockForUpdate()->first();
    }

    /**
     * Tổng số cuốn độc giả đang mượn chưa trả.
     */
    public function countBooksBorrowing(int $readerId): int
    {
        return (int) DB::table('chi_tiet_phieu_muon as ct')
            ->join('phieu_muon as pm', 'pm.id', '=', 'ct.phieu_muon_id')
            ->where('pm.doc_gia_id', $readerId)
            ->where('pm.trang_thai', self::STATUS_BORROWING)
            ->sum('ct.so_luong');
    }

    public function createTicket(int $readerId, int $userId, $borrowedAt, $dueAt): int
    {
        return DB::table('phieu_muon')->insertGetId([
            'doc_gia_id' => $readerId,
            'user_id'    => $userId,
            'ngay_muon'  => $borrowedAt,
            'han_tra'    => $dueAt,
            'trang_thai' => self::STATUS_BORROWING,
            'created_at' => $borrowedAt,
            'updated_at' => $borrowedAt,
        ]);
    }

    public function addTicketItem(int $ticketId, int $bookId, int $quantity): void
    {
        DB::table('chi_tiet_phieu_muon')->insert([
            'phieu_muon_id' => $ticketId,
            'sach_id'       => $bookId,
            'so_luong'      => $quantity,
            'tien_pat'      => 0.00,
        ]);
    }

    /**
     * Khóa phiếu mượn khi xử lý trả sách để tránh trả hai lần đồng thời.
     */
    public function findTicketForUpdate(int $id)
    {
        return DB::table('phieu_muon')->where('id', $id)->lockForUpdate()->first();
    }

    public function getTicketItems(int $ticketId): Collection
    {
        return DB::table('chi_tiet_phieu_muon as ct')
            ->join('sach as s', 's.id', '=', 'ct.sach_id')
            ->where('ct.phieu_muon_id', $ticketId)
            ->select('ct.id', 'ct.sach_id', 's.ten_sach', 'ct.so_luong', 'ct.tien_pat')
            ->orderBy('ct.id')
            ->get();
    }

    public function updateItemFine(int $itemId, float $fine): void
    {
        DB::table('chi_tiet_phieu_muon')
            ->where('id', $itemId)
            ->update(['tien_pat' => $fine, 'updated_at' => now()]);
    }

    public function markReturned(int $ticketId, $returnedAt): void
    {
        DB::table('phieu_muon')
            ->where('id', $ticketId)
            ->update([
                'trang_thai' => self::STATUS_RETURNED,
                'ngay_tra'   => $returnedAt,
                'updated_at' => $returnedAt,
            ]);
    }
}
