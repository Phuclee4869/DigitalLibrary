<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user || !in_array($user->role_id, $roles)) {
            // Ném lỗi 403 để app.php xử lý thành JSON
            throw new AccessDeniedHttpException(
                "Tài khoản không có quyền thực hiện thao tác này."
            );
        }

        return $next($request);
    }
}