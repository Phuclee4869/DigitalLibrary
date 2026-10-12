<?php

namespace App\Repositories;

use App\Support\SearchSupport;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BookRepository
{
    public function getBooks(int $limit = 10)
    {
        return DB::table('sach')->paginate($limit);
    }

    public function findBookById($id)
    {
        return DB::table('sach')->where('id', $id)->first();
    }

    /**
     * Khóa dòng các cuốn sách theo ID tăng dần (Pessimistic Lock chống Deadlock)
     */
    public function getBooksForUpdate(array $ids): Collection
    {
        return DB::table('sach')
            ->whereIn('id', $ids)
            ->orderBy('id', 'asc')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');
    }

    /**
     * Trừ tồn kho an toàn (Atomic Decrement)
     */
    public function decreaseStock(int $id, int $amount): bool
    {
        return DB::table('sach')
            ->where('id', $id)
            ->where('so_luong_con_lai', '>=', $amount)
            ->decrement('so_luong_con_lai', $amount) > 0;
    }

    /**
     * Cộng lại kho khi trả sách
     */
    public function increaseStock(int $id, int $amount): void
    {
        DB::table('sach')
            ->where('id', $id)
            ->increment('so_luong_con_lai', $amount);
    }

    /**
     * Buổi 7 - Tìm kiếm, lọc, sắp xếp và phân trang danh sách sách.
     * - Mỗi từ khóa phải khớp tên sách HOẶC tác giả; các từ khóa kết hợp bằng AND.
     * - Sắp xếp luôn kèm id làm khóa phụ để thứ tự ổn định => không lặp/sót bản ghi giữa các trang.
     */
    public function searchBooks(array $f, int $perPage, int $page): LengthAwarePaginator
    {
        $query = DB::table('sach')->select(
            'id', 'ten_sach', 'tac_gia', 'category_id', 'so_luong_con_lai', 'created_at'
        );

        foreach (SearchSupport::splitTerms($f['keyword'] ?? null) as $term) {
            $like = '%' . SearchSupport::escapeLike($term) . '%';
            $query->where(function ($w) use ($like) {
                $w->whereRaw("ten_sach LIKE ? ESCAPE '!'", [$like])
                  ->orWhereRaw("tac_gia LIKE ? ESCAPE '!'", [$like]);
            });
        }

        if (!empty($f['category_id'])) {
            $query->where('category_id', (int) $f['category_id']);
        }

        if (array_key_exists('available', $f) && $f['available'] !== null) {
            filter_var($f['available'], FILTER_VALIDATE_BOOLEAN)
                ? $query->where('so_luong_con_lai', '>', 0)
                : $query->where('so_luong_con_lai', '=', 0);
        }

        $sort  = $f['sort'] ?? 'id';
        $order = ($f['order'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

        $query->orderBy($sort, $order);
        if ($sort !== 'id') {
            $query->orderBy('id', 'asc'); // khóa phụ cố định
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
