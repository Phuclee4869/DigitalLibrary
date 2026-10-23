<?php
namespace App\Http\Controllers;

use App\Models\DocGia;
use Illuminate\Http\Request;

class DocGiaController extends Controller
{
    

    public function index()
    {
        $docGias = DocGia::paginate(10);
        return view('admin.doc_gia.index', compact('docGias'));
    }

    public function create()
    {
        return view('admin.doc_gia.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ho_ten' => 'required|string|max:100',
            'ngay_sinh' => 'nullable|date',
            'dia_chi' => 'nullable|string',
            'email' => 'nullable|email',
            'so_dien_thoai' => 'nullable|string|max:20',
            'ngay_dang_ky' => 'required|date',
            'ngay_het_han' => 'nullable|date|after_or_equal:ngay_dang_ky',
            'trang_thai' => 'boolean',
        ]);

        DocGia::create($request->all());
        
        return redirect()->route('doc-gia.index')->with('success', 'Thêm độc giả thành công!');
    }

    public function show(DocGia $docGia)
    {
        $docGia->load('phieuMuons.chiTietPhieuMuons.sach');
        return view('admin.doc_gia.show', compact('docGia'));
    }

    public function edit(DocGia $docGia)
    {
        return view('admin.doc_gia.edit', compact('docGia'));
    }

    public function update(Request $request, DocGia $docGia)
    
    {
        $request->validate([
            'ho_ten' => 'required|string|max:100',
            'ngay_sinh' => 'nullable|date',
            'dia_chi' => 'nullable|string',
            'email' => 'nullable|email',
            'so_dien_thoai' => 'nullable|string|max:20',
            'ngay_dang_ky' => 'required|date',
            'ngay_het_han' => 'nullable|date|after_or_equal:ngay_dang_ky',
            'trang_thai' => 'boolean',
        ]);

        $docGia->update($request->all());
        
        return redirect()->route('doc-gia.index')->with('success', 'Cập nhật độc giả thành công!');
    }

    public function destroy(DocGia $docGia)
    {
        // Kiểm tra xem độc giả có phiếu mượn không
        if ($docGia->phieuMuons()->count() > 0) {
            return redirect()->route('doc-gia.index')->with('error', 'Không thể xóa độc giả này vì có phiếu mượn liên quan!');
        }
        
        $docGia->delete();
        
        return redirect()->route('doc-gia.index')->with('success', 'Xóa độc giả thành công!');
    }
}