<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Exceptions\BusinessException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware) {

        // ==========================================
        // MIDDLEWARE PHÂN QUYỀN
        // ==========================================
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'api.auth' => \App\Http\Middleware\ApiAuthenticate::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {

        // ==========================================
        // LỖI NGHIỆP VỤ (V3) → JSON chuẩn hóa + ghi nhật ký (Buổi 7)
        // ==========================================
        $exceptions->renderable(function (BusinessException $e, Request $request) {
            if ($request->is('api/*')) {
                // Giao dịch nghiệp vụ đã rollback nên dòng nhật ký này vẫn được lưu
                app(\App\Services\ActivityLogService::class)->record(
                    'error.business', null, null, $e->getMessage(),
                    ['error_code' => $e->getErrorCode(), 'http_status' => $e->getHttpStatus(),
                     'method' => $request->method(), 'path' => $request->path()]
                );

                return response()->json([
                    'success'    => false,
                    'error_code' => $e->getErrorCode(),
                    'message'    => $e->getMessage(),
                    'details'    => $e->getDetails(),
                    'timestamp'  => now()->toIso8601ZuluString(),
                ], $e->getHttpStatus());
            }
        });

        // ==========================================
        // CHƯA ĐĂNG NHẬP → 401
        // ==========================================
        $exceptions->render(function (
            AuthenticationException $e,
            Request $request
        ) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'UNAUTHENTICATED',
                    'message' => 'Bạn chưa đăng nhập.'
                ], 401);
            }
        });

        // ==========================================
        // VALIDATION → 422
        // ==========================================
        $exceptions->render(function (
            ValidationException $e,
            Request $request
        ) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'VALIDATION_FAILED',
                    'message' => 'Dữ liệu không hợp lệ.',
                    'details' => $e->errors()
                ], 422);
            }
        });

        // ==========================================
        // NOT FOUND → 404
        // ==========================================
        $exceptions->render(function (
            NotFoundHttpException $e,
            Request $request
        ) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'NOT_FOUND',
                    'message' => 'Không tìm thấy dữ liệu yêu cầu.'
                ], 404);
            }
        });
    })

    ->create();