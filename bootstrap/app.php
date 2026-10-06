<?php

use App\Exceptions\BusinessException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Xử lý lỗi nghiệp vụ V3 thành JSON chuẩn hóa thống nhất
        $exceptions->renderable(function (BusinessException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success'    => false,
                    'error_code' => $e->getErrorCode(),
                    'message'    => $e->getMessage(),
                    'details'    => $e->getDetails(),
                    'timestamp'  => now()->toIso8601ZuluString(),
                ], $e->getHttpStatus());
            }
        });
    })->create();
