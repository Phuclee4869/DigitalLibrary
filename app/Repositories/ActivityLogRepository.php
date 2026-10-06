<?php

namespace App\Repositories;

use App\Support\SearchSupport;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ActivityLogRepository
{
    public function insert(array $row): void
    {
        DB::table('nhat_ky_he_thong')->insert($row);
    }

    /**
     * Tra cứu nhật ký: lọc theo người thực hiện, hành động, đối tượng, khoảng ngày, từ khóa mô tả.
     * Mặc định mới nhất trước; id là khóa phụ để thứ tự luôn xác định.
     */
    public function search(array $f, int $perPage, int $page): LengthAwarePaginator
    {
        $query = DB::table('nhat_ky_he_thong as nk')
            ->leftJoin('users as u', 'u.id', '=', 'nk.user_id')
            ->select(
                'nk.id', 'nk.user_id', 'u.email as user_email', 'nk.hanh_dong', 'nk.doi_tuong',
                'nk.doi_tuong_id', 'nk.mo_ta', 'nk.du_lieu', 'nk.dia_chi_ip', 'nk.created_at'
            );

        foreach (SearchSupport::splitTerms($f['keyword'] ?? null) as $term) {
            $query->whereRaw("nk.mo_ta LIKE ? ESCAPE '!'", ['%' . SearchSupport::escapeLike($term) . '%']);
        }

        if (!empty($f['user_id'])) {
            $query->where('nk.user_id', (int) $f['user_id']);
        }
        if (!empty($f['hanh_dong'])) {
            // "borrow" hoặc "borrow." khớp theo nhóm; "borrow.create" khớp chính xác
            $action = rtrim($f['hanh_dong'], '.');
            $query->where(function ($w) use ($action) {
                $w->where('nk.hanh_dong', $action)
                  ->orWhereRaw("nk.hanh_dong LIKE ? ESCAPE '!'", [SearchSupport::escapeLike($action) . '.%']);
            });
        }
        if (!empty($f['doi_tuong'])) {
            $query->where('nk.doi_tuong', $f['doi_tuong']);
        }
        if (!empty($f['doi_tuong_id'])) {
            $query->where('nk.doi_tuong_id', (int) $f['doi_tuong_id']);
        }
        if (!empty($f['from'])) {
            $query->where('nk.created_at', '>=', $f['from'] . ' 00:00:00');
        }
        if (!empty($f['to'])) {
            $query->where('nk.created_at', '<=', $f['to'] . ' 23:59:59');
        }

        $order = ($f['order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        return $query
            ->orderBy('nk.created_at', $order)
            ->orderBy('nk.id', $order)
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
