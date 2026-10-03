<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BorrowRepository
{
    public const STATUS_BORROWING = 'đang mượn';
    public const STATUS_RETURNED  = 'đã trả';

    public function findReaderForUpdate(int $id)
    {
        return DB::table('doc_gia')->where('id', $id)->lockForUpdate()->first();
    }

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
            'doc_gia_id'     => $readerId,
            'user_id'        => $userId,
            'ngay_muon'      => $borrowedAt,
            'han_tra'        => $dueAt,
            'trang_thai'     => self::STATUS_BORROWING,
            'so_lan_gia_han' => 0,
            'created_at'     => $borrowedAt,
            'updated_at'     => $borrowedAt,
        ]);
    }

    public function addTicketItem(int $ticketId, int $bookId, int $quantity): void
    {
        DB::table('chi_tiet_phieu_muon')->insert([
            'phieu_muon_id' => $ticketId,
            'sach_id'        => $bookId,
            'so_luong'        => $quantity,
            'tien_pat'       => 0.00,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }

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

    /**
     * Cập nhật gia hạn phiếu mượn (Luồng 3)
     */
    public function renewTicket(int $ticketId, string $newDueDate): void
    {
        DB::table('phieu_muon')
            ->where('id', $ticketId)
            ->update([
                'han_tra'        => $newDueDate,
                'so_lan_gia_han' => DB::raw('so_lan_gia_han + 1'),
                'updated_at'     => now(),
            ]);
    }
}
