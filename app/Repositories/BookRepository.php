```php
<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class BookRepository
{
    /**
     * Lấy danh sách sách
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
        return DB::table('sach')
            ->where('id', $id)
            ->first();
    }

    /**
     * Cập nhật số lượng sách còn lại trong kho khi mượn/trả
     *
     * amount > 0: trả sách → tăng số lượng
     * amount < 0: mượn sách → giảm số lượng
     */
    public function updateQuantity($id, $amount)
    {
        // Lấy sách hiện tại
        $book = DB::table('sach')
            ->where('id', $id)
            ->first();

        // Không tìm thấy sách
        if (!$book) {
            return false;
        }

        // Tính số lượng mới
        $newQuantity = $book->so_luong_con_lai + $amount;

        // Không cho số lượng âm
        if ($newQuantity < 0) {
            return false;
        }

        // Cập nhật số lượng
        DB::table('sach')
            ->where('id', $id)
            ->update([
                'so_luong_con_lai' => $newQuantity
            ]);

        return true;
    }
}
```
