<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DanhMuc;
use App\Models\Sach;

class LoginController extends Controller
{
    // Bỏ trait AuthenticatesUsers

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        try {
            $danhMucs = DanhMuc::withCount('sachs')->get();
            $totalBooks = Sach::count();
            return view('auth.login', compact('danhMucs', 'totalBooks'));
        } catch (\Exception $e) {
            return view('auth.login');
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirect dựa trên role của người dùng
            if (Auth::user()->isAdmin() || Auth::user()->isThuThu()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}