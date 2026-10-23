<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\DanhMuc;
use App\Models\Sach;

class ForgotPasswordController extends Controller
{
    // Bỏ trait SendsPasswordResetEmails

    public function __construct()
    {
        $this->middleware('guest');
    }
    
    public function showLinkRequestForm()
    {
        try {
            $danhMucs = DanhMuc::withCount('sachs')->get();
            $totalBooks = Sach::count();
            return view('auth.passwords.email', compact('danhMucs', 'totalBooks'));
        } catch (\Exception $e) {
            return view('auth.passwords.email');
        }
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }
}