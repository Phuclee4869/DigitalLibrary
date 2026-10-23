<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\DanhMuc;
use App\Models\Sach;

class RegisterController extends Controller
{
    // Bỏ trait RegistersUsers

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        try {
            $danhMucs = DanhMuc::withCount('sachs')->get();
            $totalBooks = Sach::count();
            return view('auth.register', compact('danhMucs', 'totalBooks'));
        } catch (\Exception $e) {
            return view('auth.register');
        }
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 3, // Mặc định là độc giả (Role ID: 3)
        ]);

        Auth::login($user);

        return redirect('/');
    }
}