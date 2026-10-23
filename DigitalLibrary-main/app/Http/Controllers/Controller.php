<?php

namespace App\Http\Controllers;

use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

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
                    throw new DomainException("Sách hiện đã hết hoặc không tồn tại.");
                }

                // Trừ số lượng tồn kho
                DB::table('books')
                    ->where('id', $bookId)
                    ->decrement('stock', 1);

                return response()->json(['message' => 'Mượn sách thành công!']);
            });
        } catch (DomainException $e) {
            // Lỗi nghiệp vụ do mình chủ động ném: an toàn để hiển thị
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (Throwable $e) {
            // Lỗi hệ thống (CSDL...): chỉ ghi log, không trả chi tiết cho người dùng
            Log::error('borrowBookWithLock', [
                'book_id' => $bookId,
                'error'   => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Không thể mượn sách lúc này.'], 500);
        }
    }
}