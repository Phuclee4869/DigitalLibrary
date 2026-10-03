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

    public function updateQuantity($id, $amount)
    {
        $book = DB::table('sach')
            ->where('id', $id)
            ->first();

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