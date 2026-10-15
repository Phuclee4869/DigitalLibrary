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
     * Buổi 9 - Trừ kho NHIỀU đầu sách bằng MỘT câu UPDATE (thay cho một UPDATE mỗi đầu sách).
     * Điều kiện "còn đủ" được kiểm cho từng dòng; trả false nếu có dòng không được cập nhật.
     * Gọi bên trong giao dịch đã khóa các dòng sách (getBooksForUpdate).
     *
     * @param array<int,int> $qtyByBook  [sach_id => số lượng]
     */
    public function decreaseStockBulk(array $qtyByBook): bool
    {
        if (!$qtyByBook) {
            return true;
        }

        [$case, $caseBindings] = $this->caseByBook($qtyByBook);
        $ids = array_keys($qtyByBook);

        $sql = "UPDATE sach SET so_luong_con_lai = so_luong_con_lai - {$case}"
             . ' WHERE id IN (' . implode(',', array_fill(0, count($ids), '?')) . ')'
             . " AND so_luong_con_lai >= {$case}";

        $affected = DB::update($sql, array_merge($caseBindings, $ids, $caseBindings));

        return $affected === count($ids);
    }

    /**
     * Buổi 9 - Cộng kho NHIỀU đầu sách bằng MỘT câu UPDATE khi trả sách.
     *
     * @param array<int,int> $qtyByBook  [sach_id => số lượng]
     */
    public function increaseStockBulk(array $qtyByBook): void
    {
        if (!$qtyByBook) {
            return;
        }

        [$case, $caseBindings] = $this->caseByBook($qtyByBook);
        $ids = array_keys($qtyByBook);

        $sql = "UPDATE sach SET so_luong_con_lai = so_luong_con_lai + {$case}"
             . ' WHERE id IN (' . implode(',', array_fill(0, count($ids), '?')) . ')';

        DB::update($sql, array_merge($caseBindings, $ids));
    }

    /**
     * Biểu thức "CASE id WHEN ? THEN ? ... END" và các giá trị ràng buộc (không nối chuỗi từ đầu vào).
     *
     * @return array{0: string, 1: array<int,int>}
     */
    private function caseByBook(array $qtyByBook): array
    {
        $sql = 'CASE id';
        $bindings = [];

        foreach ($qtyByBook as $id => $qty) {
            $sql .= ' WHEN ? THEN ?';
            $bindings[] = (int) $id;
            $bindings[] = (int) $qty;
        }

        return [$sql . ' END', $bindings];
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
