<?php

namespace App\Repositories;

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
}
