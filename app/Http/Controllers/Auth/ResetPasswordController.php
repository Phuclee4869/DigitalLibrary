<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\DanhMuc;
use App\Models\Sach;

class ResetPasswordController extends Controller
{
    // Bỏ trait ResetsPasswords

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest');
    }
    
    public function showResetForm(Request $request, $token = null)
    {
        try {
            $danhMucs = DanhMuc::withCount('sachs')->get();
            $totalBooks = Sach::count();
            
            return view('auth.passwords.reset', [
                'token' => $token,
                'email' => $request->email,
                'danhMucs' => $danhMucs,
                'totalBooks' => $totalBooks
            ]);
        } catch (\Exception $e) {
            return view('auth.passwords.reset', [
                'token' => $token,
                'email' => $request->email
            ]);
        }
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }
}