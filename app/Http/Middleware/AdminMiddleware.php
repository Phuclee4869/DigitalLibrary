<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Log để debug
        Log::info('AdminOrThuThu middleware running');
        Log::info('User authenticated: ' . (Auth::check() ? 'Yes' : 'No'));
        if (Auth::check()) {
            Log::info('User role_id: ' . Auth::user()->role_id);
            Log::info('Is Admin: ' . (Auth::user()->isAdmin() ? 'Yes' : 'No'));
            Log::info('Is ThuThu: ' . (Auth::user()->isThuThu() ? 'Yes' : 'No'));
        }

        // Kiểm tra quyền truy cập
        if (!Auth::check() || (!Auth::user()->isAdmin() && !Auth::user()->isThuThu())) {
            Log::warning('Access denied in AdminOrThuThu middleware');
            
            // Nếu đang gọi AJAX, trả về JSON
            if ($request->ajax()) {
                return response()->json(['error' => 'Không có quyền truy cập'], 403);
            }
            
            // Nếu không, chuyển hướng
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập trang này!');
        }

        return $next($request);
    }
}