<?php

namespace App\Repositories;

use App\Support\SearchSupport;
use Illuminate\Pagination\LengthAwarePaginator;
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

    /**
     * Buổi 8 - Lấy phiếu mượn kèm tài khoản sở hữu (doc_gia.user_id) để kiểm quyền đối tượng.
     * Không khóa dòng: chỉ phục vụ kiểm quyền; nghiệp vụ vẫn khóa lại bên trong giao dịch.
     */
    public function findTicketWithOwner(int $id)
    {
        return DB::table('phieu_muon as pm')
            ->join('doc_gia as dg', 'dg.id', '=', 'pm.doc_gia_id')
            ->where('pm.id', $id)
            ->select('pm.*', 'dg.user_id as chu_so_huu_user_id')
            ->first();
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

    /**
     * Buổi 7 - Tìm kiếm, lọc và phân trang phiếu mượn.
     */
    public function searchTickets(array $f, int $perPage, int $page): LengthAwarePaginator
    {
        $totalBooks = DB::table('chi_tiet_phieu_muon')
            ->selectRaw('phieu_muon_id, SUM(so_luong) as tong_so_sach')
            ->groupBy('phieu_muon_id');

        $query = DB::table('phieu_muon as pm')
            ->join('doc_gia as dg', 'dg.id', '=', 'pm.doc_gia_id')
            ->leftJoinSub($totalBooks, 'ct', 'ct.phieu_muon_id', '=', 'pm.id')
            ->select(
                'pm.id', 'pm.doc_gia_id', 'dg.ho_ten', 'pm.ngay_muon', 'pm.han_tra',
                'pm.ngay_tra', 'pm.trang_thai', 'pm.so_lan_gia_han',
                DB::raw('COALESCE(ct.tong_so_sach, 0) as tong_so_sach')
            );

        foreach (SearchSupport::splitTerms($f['keyword'] ?? null) as $term) {
            $query->whereRaw("dg.ho_ten LIKE ? ESCAPE '!'", ['%' . SearchSupport::escapeLike($term) . '%']);
        }

        if (!empty($f['doc_gia_id'])) {
            $query->where('pm.doc_gia_id', (int) $f['doc_gia_id']);
        }
        if (!empty($f['trang_thai'])) {
            $query->where('pm.trang_thai', $f['trang_thai']);
        }
        if (!empty($f['overdue']) && filter_var($f['overdue'], FILTER_VALIDATE_BOOLEAN)) {
            $query->where('pm.trang_thai', self::STATUS_BORROWING)->where('pm.han_tra', '<', now());
        }
        if (!empty($f['from'])) {
            $query->where('pm.ngay_muon', '>=', $f['from'] . ' 00:00:00');
        }
        if (!empty($f['to'])) {
            $query->where('pm.ngay_muon', '<=', $f['to'] . ' 23:59:59');
        }

        $sort  = $f['sort'] ?? 'id';
        $order = ($f['order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $query->orderBy('pm.' . $sort, $order);
        if ($sort !== 'id') {
            $query->orderBy('pm.id', 'desc');
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
