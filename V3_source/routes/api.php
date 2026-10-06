<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowController;


// ===============================
// API LẤY DANH SÁCH SÁCH
// ===============================
Route::get('/v1/books', [BookController::class, 'index']);


// ===============================
// API ĐĂNG NHẬP
// ===============================
Route::post('/v1/login', function (Request $request) {

    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'success' => false,
            'message' => 'Email hoặc mật khẩu không đúng.'
        ], 401);
    }

    $token = $user->createToken('postman-test')->plainTextToken;

    return response()->json([
        'success' => true,
        'message' => 'Đăng nhập thành công.',
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'email' => $user->email,
            'role_id' => $user->role_id,
        ]
    ]);
});


// ===============================
// API YÊU CẦU ĐĂNG NHẬP
// ===============================
Route::middleware(['auth:sanctum'])->group(function () {

    // ---- NGHIỆP VỤ (V3): chỉ Admin (1) và Thủ thư (2) ----
    Route::middleware('role:1,2')->group(function () {

        // Luồng 1: Lập phiếu mượn
        Route::post('/v1/borrow-tickets', [BorrowController::class, 'store']);

        // Luồng 2: Trả sách
        Route::post('/v1/borrow-tickets/{id}/return', [BorrowController::class, 'returnBooks'])
            ->whereNumber('id');
    });

    // Xóa người dùng - chỉ Admin
    Route::delete('/v1/users/{id}', function ($id) {
        return response()->json([
            'success' => true,
            'message' => 'Đã xóa người dùng',
            'user_id' => $id
        ]);
    })->middleware('role:1');
});
