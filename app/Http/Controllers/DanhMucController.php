<?php
namespace App\Http\Controllers;

use App\Models\DanhMuc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DanhMucController extends Controller
{
    public function __construct()
    {
        // Chỉ áp dụng middleware auth, không thêm kiểm tra quyền truy cập trong các phương thức
        $this->middleware('auth');
        // Không sử dụng middleware admin.or.thuthu ở đây, chúng ta sẽ đặt nó trong route
    }

    public function index()
    {
        $danhMucs = DanhMuc::paginate(10);
        return view('admin.danh_muc.index', compact('danhMucs'));
    }

    public function create()
    {
        try {
            Log::info('Accessing danh_muc create method');
            return view('admin.danh_muc.create');
        } catch (\Exception $e) {
            Log::error('Error in danh_muc create method: ' . $e->getMessage());
            return redirect()->route('danh-muc.index')->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'ten_danh_muc' => 'required|string|max:100|unique:danh_muc',
                'mo_ta' => 'nullable|string',
            ]);

            DanhMuc::create($request->all());
            
            return redirect()->route('danh-muc.index')->with('success', 'Thêm danh mục thành công!');
        } catch (\Exception $e) {
            Log::error('Error in danh_muc store method: ' . $e->getMessage());
            return redirect()->route('danh-muc.create')->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage())->withInput();
        }
    }

    // Các phương thức khác giữ nguyên...
    public function show(DanhMuc $danhMuc)
    {
        return view('admin.danh_muc.show', compact('danhMuc'));
    }

    public function edit(DanhMuc $danhMuc)
    {
        try {
            return view('admin.danh_muc.edit', compact('danhMuc'));
        } catch (\Exception $e) {
            Log::error('Error in danh_muc edit method: ' . $e->getMessage());
            return redirect()->route('danh-muc.index')->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    public function update(Request $request, DanhMuc $danhMuc)
    {
        try {
            $request->validate([
                'ten_danh_muc' => 'required|string|max:100|unique:danh_muc,ten_danh_muc,' . $danhMuc->id,
                'mo_ta' => 'nullable|string',
            ]);

            $danhMuc->update($request->all());
            
            return redirect()->route('danh-muc.index')->with('success', 'Cập nhật danh mục thành công!');
        } catch (\Exception $e) {
            Log::error('Error in danh_muc update method: ' . $e->getMessage());
            return redirect()->route('danh-muc.edit', $danhMuc)->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(DanhMuc $danhMuc)
    {
        try {
            // Kiểm tra xem danh mục có sách không
            if ($danhMuc->sachs()->count() > 0) {
                return redirect()->route('danh-muc.index')->with('error', 'Không thể xóa danh mục này vì có sách thuộc danh mục!');
            }
            
            $danhMuc->delete();
            
            return redirect()->route('danh-muc.index')->with('success', 'Xóa danh mục thành công!');
        } catch (\Exception $e) {
            Log::error('Error in danh_muc destroy method: ' . $e->getMessage());
            return redirect()->route('danh-muc.index')->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
}