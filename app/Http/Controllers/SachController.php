<?php
namespace App\Http\Controllers;

use App\Models\Sach;
use App\Models\DanhMuc;
use App\Models\TacGia;
use App\Models\NhaXuatBan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SachController extends Controller
{
    public function __construct()
    {
        // Chỉ áp dụng middleware, không thêm kiểm tra quyền truy cập trong các phương thức
        $this->middleware('auth');
        // Không sử dụng middleware admin.or.thuthu ở đây, chúng ta sẽ đặt nó trong route
    }

    public function index()
    {
        $sachs = Sach::with(['danhMuc', 'tacGia', 'nhaXuatBan'])->paginate(10);
        return view('admin.sach.index', compact('sachs'));
    }

    public function create()
    {
        try {
            Log::info('Accessing sach create method');
            $danhMucs = DanhMuc::all();
            $tacGias = TacGia::all();
            $nhaXuatBans = NhaXuatBan::all();
            return view('admin.sach.create', compact('danhMucs', 'tacGias', 'nhaXuatBans'));
        } catch (\Exception $e) {
            Log::error('Error in sach create method: ' . $e->getMessage());
            return redirect()->route('sach.index')->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'tieu_de' => 'required|string|max:255',
                'danh_muc_id' => 'required|exists:danh_muc,id',
                'tac_gia_id' => 'required|exists:tac_gia,id',
                'nha_xuat_ban_id' => 'required|exists:nha_xuat_ban,id',
                'isbn' => 'nullable|string|max:20',
                'so_trang' => 'nullable|integer|min:1',
                'nam_xuat_ban' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
                'mo_ta' => 'nullable|string',
                'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'so_luong' => 'required|integer|min:0',
                'gia' => 'nullable|numeric|min:0',
            ]);

            $data = $request->all();
            
            // Xử lý upload hình ảnh
            if ($request->hasFile('hinh_anh')) {
                $file = $request->file('hinh_anh');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/sach', $fileName);
                $data['hinh_anh'] = 'sach/' . $fileName;
            }
            
            // Mặc định số lượng còn lại bằng số lượng ban đầu
            $data['so_luong_con_lai'] = $data['so_luong'];

            Sach::create($data);
            
            return redirect()->route('sach.index')->with('success', 'Thêm sách thành công!');
        } catch (\Exception $e) {
            Log::error('Error in sach store method: ' . $e->getMessage());
            return redirect()->route('sach.create')->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage())->withInput();
        }
    }

    // Các phương thức khác giữ nguyên...
    public function show(Sach $sach)
    {
        return view('admin.sach.show', compact('sach'));
    }

    public function edit(Sach $sach)
    {
        try {
            $danhMucs = DanhMuc::all();
            $tacGias = TacGia::all();
            $nhaXuatBans = NhaXuatBan::all();
            return view('admin.sach.edit', compact('sach', 'danhMucs', 'tacGias', 'nhaXuatBans'));
        } catch (\Exception $e) {
            Log::error('Error in sach edit method: ' . $e->getMessage());
            return redirect()->route('sach.index')->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Sach $sach)
    {
        try {
            $request->validate([
                'tieu_de' => 'required|string|max:255',
                'danh_muc_id' => 'required|exists:danh_muc,id',
                'tac_gia_id' => 'required|exists:tac_gia,id',
                'nha_xuat_ban_id' => 'required|exists:nha_xuat_ban,id',
                'isbn' => 'nullable|string|max:20',
                'so_trang' => 'nullable|integer|min:1',
                'nam_xuat_ban' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
                'mo_ta' => 'nullable|string',
                'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'so_luong' => 'required|integer|min:0',
                'gia' => 'nullable|numeric|min:0',
            ]);

            $data = $request->all();
            
            // Xử lý upload hình ảnh
            if ($request->hasFile('hinh_anh')) {
                // Xóa hình ảnh cũ nếu có
                if ($sach->hinh_anh) {
                    Storage::delete('public/' . $sach->hinh_anh);
                }
                
                $file = $request->file('hinh_anh');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/sach', $fileName);
                $data['hinh_anh'] = 'sach/' . $fileName;
            }
            
            // Tính toán số lượng còn lại khi thay đổi tổng số lượng
            $soLuongMuon = $sach->so_luong - $sach->so_luong_con_lai;
            $data['so_luong_con_lai'] = $data['so_luong'] - $soLuongMuon;
            
            $sach->update($data);
            
            return redirect()->route('sach.index')->with('success', 'Cập nhật sách thành công!');
        } catch (\Exception $e) {
            Log::error('Error in sach update method: ' . $e->getMessage());
            return redirect()->route('sach.edit', $sach)->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Sach $sach)
    {
        try {
            // Kiểm tra xem sách có đang được mượn không
            if ($sach->chiTietPhieuMuons()->whereHas('phieuMuon', function($q) {
                $q->where('trang_thai', 'đang mượn');
            })->exists()) {
                return redirect()->route('sach.index')->with('error', 'Không thể xóa sách này vì đang có người mượn!');
            }
            
            // Xóa hình ảnh
            if ($sach->hinh_anh) {
                Storage::delete('public/' . $sach->hinh_anh);
            }
            
            $sach->delete();
            
            return redirect()->route('sach.index')->with('success', 'Xóa sách thành công!');
        } catch (\Exception $e) {
            Log::error('Error in sach destroy method: ' . $e->getMessage());
            return redirect()->route('sach.index')->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
}