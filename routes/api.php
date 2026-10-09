<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
 * Buổi 8 - V3: routes/api.php đã gộp thành MỘT khối duy nhất
 * (bản trước có hai khối `use` trùng nhau và route /books, /login, /borrow-tickets khai báo hai lần).
 *
 * Mỗi điểm cuối ghi rõ: kiểm quyền chức năng (middleware role) + kiểm quyền đối tượng (ObjectAccessService).
 */
Route::prefix('v1')->group(function () {

    // [Công khai] Đăng nhập – ghi nhật ký thành công / thất bại, không ghi mật khẩu
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

    // [Công khai] Tìm kiếm sách – giới hạn 60 yêu cầu/phút (Buổi 7)
    Route::get('/books', [SearchController::class, 'books'])->middleware('throttle:60,1');

    Route::middleware(['auth:sanctum'])->group(function () {

        // Admin (1), Thủ thư (2)
        Route::middleware('role:1,2')->group(function () {
            // Buổi 8: trước đây KHÔNG yêu cầu đăng nhập/vai trò -> nay chỉ Admin, Thủ thư
            Route::post('/books', [BookController::class, 'store']);

            Route::get('/borrow-tickets', [SearchController::class, 'tickets']);                                       // Buổi 7
            Route::post('/borrow-tickets', [BorrowController::class, 'store']);                                        // Luồng 1
            Route::post('/borrow-tickets/{id}/return', [BorrowController::class, 'returnBooks'])->whereNumber('id');   // Luồng 2 + kiểm quyền đối tượng
            Route::post('/borrow-tickets/{id}/renew', [BorrowController::class, 'renew'])->whereNumber('id');          // Luồng 3 + kiểm quyền đối tượng
        });

        // Buổi 8: xem chi tiết phiếu – mọi vai trò đăng nhập, nhưng Độc giả (3) chỉ xem phiếu của mình
        Route::get('/borrow-tickets/{id}', [BorrowController::class, 'show'])
            ->whereNumber('id')
            ->middleware('role:1,2,3');

        // Chỉ Admin (1)
        Route::middleware('role:1')->group(function () {
            Route::get('/activity-logs', [SearchController::class, 'activityLogs']);                                   // Buổi 7
            Route::delete('/users/{id}', [UserController::class, 'destroy'])->whereNumber('id');                       // Buổi 8: kiểm quyền đối tượng
        });
    });
});
