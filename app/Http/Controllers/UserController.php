<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
  
    public function index()
    {
        $users = User::with('role')->paginate(10);
        return view('admin.user.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.user.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'so_dien_thoai' => 'nullable|string|max:20',
            'dia_chi' => 'nullable|string',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'so_dien_thoai' => $request->so_dien_thoai,
            'dia_chi' => $request->dia_chi,
        ]);

        return redirect()->route('user.index')->with('success', 'Tạo người dùng thành công!');
    }

    public function show(User $user)
    {
        return view('admin.user.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.user.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'role_id' => 'required|exists:roles,id',
        'so_dien_thoai' => 'nullable|string|max:20',
        'dia_chi' => 'nullable|string',
    ]);

    $data = [
        'name' => $request->name,
        'email' => $request->email,
        'role_id' => $request->role_id,
        'so_dien_thoai' => $request->so_dien_thoai,
        'dia_chi' => $request->dia_chi,
    ];

    if ($request->filled('password')) {
        $request->validate([
            'password' => 'string|min:8|confirmed',
        ]);
        $data['password'] = Hash::make($request->password);
    }

    $user->update($data);

    return redirect()->route('user.index')->with('success', 'Cập nhật người dùng thành công!');
}

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('user.index')->with('error', 'Bạn không thể xóa chính mình!');
        }
        
        // Kiểm tra xem người dùng có phiếu mượn không
        if ($user->phieuMuons()->count() > 0) {
            return redirect()->route('user.index')->with('error', 'Không thể xóa người dùng này vì có phiếu mượn liên quan!');
        }
        
        $user->delete();
        
        return redirect()->route('user.index')->with('success', 'Xóa người dùng thành công!');
    }
}