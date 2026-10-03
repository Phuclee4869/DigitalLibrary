<?php

use App\Http\Controllers\BorrowController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {

    // CHỈ ADMIN (1) VÀ THỦ THƯ (2) ĐƯỢC THAO TÁC NGHIỆP VỤ (V3)
    Route::middleware('role:1,2')->prefix('v1')->group(function () {

        // Luồng 1: Lập phiếu mượn
        Route::post('/borrow-tickets', [BorrowController::class, 'store']);

        // Luồng 2: Trả sách & tính phạt
        Route::post('/borrow-tickets/{id}/return', [BorrowController::class, 'returnBooks'])->whereNumber('id');

        // Luồng 3: Gia hạn phiếu mượn (Bổ sung Buổi 6)
        Route::post('/borrow-tickets/{id}/renew', [BorrowController::class, 'renew'])->whereNumber('id');
    });

});
