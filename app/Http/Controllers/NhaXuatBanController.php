<?php
namespace App\Http\Controllers;

use App\Models\NhaXuatBan;
use Illuminate\Http\Request;

class NhaXuatBanController extends Controller
{
    

    public function index()
    {
        $nhaXuatBans = NhaXuatBan::paginate(10);
        return view('admin.nha_xuat_ban.index', compact('nhaXuatBans'));
    }

    public function create()
    {
        return view('admin.nha_xuat_ban.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_nxb' => 'required|string|max:100',
            'dia_chi' => 'nullable|string',
            'email' => 'nullable|email',
            'so_dien_thoai' => 'nullable|string|max:20',
        ]);

        NhaXuatBan::create($request->all());
        
        return redirect()->route('nha-xuat-ban.index')->with('success', 'Thêm nhà xuất bản thành công!');
    }

    public function show(NhaXuatBan $nhaXuatBan)
    {
        return view('admin.nha_xuat_ban.show', compact('nhaXuatBan'));
    }

    public function edit(NhaXuatBan $nhaXuatBan)
    {
        return view('admin.nha_xuat_ban.edit', compact('nhaXuatBan'));
    }

    public function update(Request $request, NhaXuatBan $nhaXuatBan)
    {
        $request->validate([
            'ten_nxb' => 'required|string|max:100',
            'dia_chi' => 'nullable|string',
            'email' => 'nullable|email',
            'so_dien_thoai' => 'nullable|string|max:20',
        ]);

        $nhaXuatBan->update($request->all());
        
        return redirect()->route('nha-xuat-ban.index')->with('success', 'Cập nhật nhà xuất bản thành công!');
    }

    public function destroy(NhaXuatBan $nhaXuatBan)
    {
        // Kiểm tra xem nhà xuất bản có sách không
        if ($nhaXuatBan->sachs()->count() > 0) {
            return redirect()->route('nha-xuat-ban.index')->with('error', 'Không thể xóa nhà xuất bản này vì có sách thuộc nhà xuất bản!');
        }
        
        $nhaXuatBan->delete();
        
        return redirect()->route('nha-xuat-ban.index')->with('success', 'Xóa nhà xuất bản thành công!');
    }
}