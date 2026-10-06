<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Tầng dữ liệu: chỉ truy vấn bảng `sach`, không chứa quy tắc nghiệp vụ.
 */
class BookRepository
{
    /**
     * Lấy danh sách sách (có phân trang)
     */
    public function getBooks($limit = 10)
    {
        return DB::table('sach')->paginate($limit);
    }

    /**
     * Tìm sách theo ID
     */
    public function findBookById($id)
    {
        return DB::table('sach')->where('id', $id)->first();
    }

    /**
     * Khóa và lấy nhiều sách cùng lúc (SELECT ... FOR UPDATE).
     * Sắp xếp theo id để hai giao dịch chạy song song không khóa chéo (deadlock).
     * Phải gọi bên trong DB::transaction.
     */
    public function getBooksForUpdate(array $ids): Collection
    {
        return DB::table('sach')
            ->whereIn('id', $ids)
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');
    }

    /**
     * Trừ kho một cách an toàn: chỉ trừ khi còn đủ số lượng.
     * Trả về false nếu không cập nhật được dòng nào (không đủ hàng).
     */
    public function decreaseStock(int $id, int $amount): bool
    {
        return DB::table('sach')
            ->where('id', $id)
            ->where('so_luong_con_lai', '>=', $amount)
            ->decrement('so_luong_con_lai', $amount) > 0;
    }

    /**
     * Cộng lại kho khi trả sách.
     */
    public function increaseStock(int $id, int $amount): void
    {
        DB::table('sach')
            ->where('id', $id)
            ->increment('so_luong_con_lai', $amount);
    }

    /**
     * Giữ lại để tương thích code cũ:
     * amount > 0: trả sách (tăng), amount < 0: mượn sách (giảm)
     */
    public function updateQuantity($id, $amount)
    {
        if ($amount < 0) {
            return $this->decreaseStock((int) $id, abs((int) $amount));
        }

        if (!$this->findBookById($id)) {
            return false;
        }

        $this->increaseStock((int) $id, (int) $amount);

        return true;
    }
}
