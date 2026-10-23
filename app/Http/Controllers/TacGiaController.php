<?php
namespace App\Http\Controllers;

use App\Models\TacGia;
use Illuminate\Http\Request;

class TacGiaController extends Controller
{
   
    public function index()
    {
        $tacGias = TacGia::all();
        return view('admin.tac_gia.index', compact('tacGias'));
    }

    public function create()
    {
        return view('admin.tac_gia.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_tac_gia' => 'required|string|max:100',
            'tieu_su' => 'nullable|string',
            'ngay_sinh' => 'nullable|date',
        ]);

        TacGia::create($request->all());
        
        return redirect()->route('tac-gia.index')->with('success', 'Thêm tác giả thành công!');
    }

    public function show(TacGia $tacGia)
    {
        return view('admin.tac_gia.show', compact('tacGia'));
    }

    public function edit(TacGia $tacGia)
    {
        return view('admin.tac_gia.edit', compact('tacGia'));
    }

    public function update(Request $request, TacGia $tacGia)
    {
        $request->validate([
            'ten_tac_gia' => 'required|string|max:100',
            'tieu_su' => 'nullable|string',
            'ngay_sinh' => 'nullable|date',
        ]);

        $tacGia->update($request->all());
        
        return redirect()->route('tac-gia.index')->with('success', 'Cập nhật tác giả thành công!');
    }

    public function destroy(TacGia $tacGia)
    {
        // Kiểm tra xem tác giả có sách không
        if ($tacGia->sachs()->count() > 0) {
            return redirect()->route('tac-gia.index')->with('error', 'Không thể xóa tác giả này vì có sách thuộc tác giả!');
        }
        
        $tacGia->delete();
        
        return redirect()->route('tac-gia.index')->with('success', 'Xóa tác giả thành công!');
    }
}