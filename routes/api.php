<?php

use App\Http\Controllers\BorrowController;
use App\Http\Controllers\SearchController;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Đăng nhập (Buổi 7: ghi nhật ký đăng nhập thành công / thất bại, không ghi mật khẩu)
    Route::post('/login', function (Request $request, ActivityLogService $logs) {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            $logs->record('auth.login_failed', 'users', $user?->id,
                'Đăng nhập thất bại', ['email' => $request->email], $user?->id);

            return response()->json([
                'success' => false,
                'message' => 'Email hoặc mật khẩu không đúng.',
            ], 401);
        }

        $token = $user->createToken('postman-test')->plainTextToken;
        $logs->record('auth.login', 'users', $user->id, 'Đăng nhập thành công', [], $user->id);

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công.',
            'token'   => $token,
            'user'    => ['id' => $user->id, 'email' => $user->email, 'role_id' => $user->role_id],
        ]);
    })->middleware('throttle:10,1');

    // Buổi 7 - Tìm kiếm sách: công khai, giới hạn 60 yêu cầu/phút
    Route::get('/books', [SearchController::class, 'books'])->middleware('throttle:60,1');

    Route::middleware(['auth:sanctum'])->group(function () {

        // Admin (1) và Thủ thư (2): nghiệp vụ + tìm kiếm phiếu mượn
        Route::middleware('role:1,2')->group(function () {
            Route::get('/borrow-tickets', [SearchController::class, 'tickets']);                       // Buổi 7
            Route::post('/borrow-tickets', [BorrowController::class, 'store']);                        // Luồng 1
            Route::post('/borrow-tickets/{id}/return', [BorrowController::class, 'returnBooks'])->whereNumber('id'); // Luồng 2
            Route::post('/borrow-tickets/{id}/renew', [BorrowController::class, 'renew'])->whereNumber('id');        // Luồng 3
        });

        // Chỉ Admin (1): tra cứu nhật ký hệ thống (Buổi 7)
        Route::get('/activity-logs', [SearchController::class, 'activityLogs'])->middleware('role:1');
    });
});
