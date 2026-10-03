<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

abstract class Controller
{
    /**
     * Xử lý giao dịch mượn sách sử dụng Khóa hàng (lockForUpdate)
     */
    public function borrowBookWithLock(Request $request, $bookId)
    {
        try {
            return DB::transaction(function () use ($bookId) {
                // Khóa hàng trong CSDL chống tranh chấp đồng thời
                $book = DB::table('books')
                    ->where('id', $bookId)
                    ->lockForUpdate()
                    ->first();

                if (!$book || $book->stock <= 0) {
                    throw new Exception("Sách hiện đã hết hoặc không tồn tại.");
                }

                // Trừ số lượng tồn kho
                DB::table('books')
                    ->where('id', $bookId)
                    ->decrement('stock', 1);

                return response()->json(['message' => 'Mượn sách thành công!']);
            });
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}