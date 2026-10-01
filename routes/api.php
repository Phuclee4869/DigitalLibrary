<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Controllers\BookController;


// ===============================
// API LẤY DANH SÁCH SÁCH
// ===============================
Route::get('/v1/books', [BookController::class, 'index']);


// ===============================
// API THÊM SÁCH
// ===============================
Route::post('/v1/books', [BookController::class, 'store']);


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
Route::middleware(['api.auth:sanctum'])->group(function () {

    // ===============================
    // API LẬP PHIẾU MƯỢN
    // Chỉ Role 1 và Role 2 được sử dụng
    // ===============================
    Route::post(
        '/v1/borrow-tickets',
        [BookController::class, 'borrow']
    )->middleware('role:1,2');


    // ===============================
    // API XÓA NGƯỜI DÙNG
    // Chỉ Admin Role 1
    // ===============================
    Route::delete('/v1/users/{id}', function ($id) {

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa người dùng',
            'user_id' => $id
        ]);

    })->middleware('role:1');
});