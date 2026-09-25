<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {
        // Đăng ký alias cho middleware phân quyền
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions): void {

        // Lỗi 422: Dữ liệu đầu vào không hợp lệ
        $exceptions->renderable(function (ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'VALIDATION_FAILED',
                    'message' => 'Dữ liệu đầu vào không hợp lệ.',
                    'details' => $e->errors(),
                    'timestamp' => now()->toIso8601ZuluString()
                ], 422);
            }
        });

        // Lỗi 401: Chưa đăng nhập
        $exceptions->renderable(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'UNAUTHORIZED',
                    'message' => 'Chưa xác thực hoặc token hết hạn',
                    'details' => null,
                    'timestamp' => now()->toIso8601ZuluString()
                ], 401);
            }
        });

        // Lỗi 403: Không đủ quyền
        $exceptions->renderable(function (AccessDeniedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'FORBIDDEN',
                    'message' => 'Tài khoản không có quyền thực hiện thao tác này',
                    'details' => null,
                    'timestamp' => now()->toIso8601ZuluString()
                ], 403);
            }
        });

        // Lỗi 404: Không tìm thấy trang/dữ liệu
        $exceptions->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'NOT_FOUND',
                    'message' => 'Không tìm thấy tài nguyên yêu cầu.',
                    'details' => null,
                    'timestamp' => now()->toIso8601ZuluString()
                ], 404);
            }
        });
    })

    ->create();