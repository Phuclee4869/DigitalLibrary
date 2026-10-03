<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class BookRepository
{
    public function getBooks($limit = 10)
    {
        return DB::table('sach')->paginate($limit);
    }

    public function findBookById($id)
    {
        return DB::table('sach')
            ->where('id', $id)
            ->first();
    }

    /**
     * Đọc sách kèm khóa hàng (SELECT ... FOR UPDATE).
     * Chỉ gọi bên trong DB::transaction().
     */
    public function findBookForUpdate($id)
    {
        return DB::table('sach')
            ->where('id', $id)
            ->lockForUpdate()
            ->first();
    }

    public function updateQuantity($id, $amount)
    {
        $book = $this->findBookForUpdate($id);

        if (!$book) {
            return false;
        }

        $newQuantity = $book->so_luong_con_lai + $amount;

        if ($newQuantity < 0) {
            return false;
        }

        DB::table('sach')
            ->where('id', $id)
            ->update([
                'so_luong_con_lai' => $newQuantity
            ]);

        return true;
    }
}
