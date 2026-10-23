<?php
namespace App\Http\Controllers;

use App\Models\PhieuMuon;
use App\Models\DocGia;
use App\Models\Sach;
use App\Models\ChiTietPhieuMuon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhieuMuonController extends Controller
{
    public function index()
    {
        $phieuMuons = PhieuMuon::with(['docGia', 'user'])->paginate(10);
        return view('admin.phieu_muon.index', compact('phieuMuons'));
    }

    public function create()
    {
        $docGias = DocGia::where('trang_thai', true)->get();
        $sachs = Sach::where('so_luong_con_lai', '>', 0)->get();
        return view('admin.phieu_muon.create', compact('docGias', 'sachs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'doc_gia_id' => 'required|exists:doc_gia,id',
            'ngay_muon' => 'required|date',
            'ngay_hen_tra' => 'required|date|after_or_equal:ngay_muon',
            'ghi_chu' => 'nullable|string',
            'sach_ids' => 'required|array',
            'sach_ids.*' => 'exists:sach,id',
            'so_luongs' => 'required|array',
            'so_luongs.*' => 'integer|min:1',
            'tinh_trang_khi_muons' => 'required|array',
            'tinh_trang_khi_muons.*' => 'string',
        ]);

        DB::beginTransaction();
        
        try {
            // Tạo phiếu mượn
            $phieuMuon = PhieuMuon::create([
                'doc_gia_id' => $request->doc_gia_id,
                'user_id' => auth()->id(),
                'ngay_muon' => $request->ngay_muon,
                'ngay_hen_tra' => $request->ngay_hen_tra,
                'trang_thai' => 'đang mượn',
                'ghi_chu' => $request->ghi_chu,
            ]);
            
            // Tạo chi tiết phiếu mượn
            foreach ($request->sach_ids as $key => $sach_id) {
                $sach = Sach::findOrFail($sach_id);
                $soLuong = $request->so_luongs[$key];
                
                // Kiểm tra số lượng sách còn lại
                if ($sach->so_luong_con_lai < $soLuong) {
                    throw new \Exception("Sách '{$sach->tieu_de}' không đủ số lượng để mượn.");
                }
                
                // Tạo chi tiết phiếu mượn
                ChiTietPhieuMuon::create([
                    'phieu_muon_id' => $phieuMuon->id,
                    'sach_id' => $sach_id,
                    'so_luong' => $soLuong,
                    'tinh_trang_khi_muon' => $request->tinh_trang_khi_muons[$key],
                ]);
                
                // Cập nhật số lượng sách còn lại
                $sach->so_luong_con_lai -= $soLuong;
                $sach->save();
            }
            
            DB::commit();
            
            return redirect()->route('phieu-muon.index')->with('success', 'Tạo phiếu mượn thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput();
        }
    }

    public function show(PhieuMuon $phieuMuon)
    {
        $phieuMuon->load(['docGia', 'user', 'chiTietPhieuMuons.sach']);
        return view('admin.phieu_muon.show', compact('phieuMuon'));
    }

    public function edit(PhieuMuon $phieuMuon)
    {
        if ($phieuMuon->trang_thai !== 'đang mượn') {
            return redirect()->route('phieu-muon.index')->with('error', 'Chỉ có thể chỉnh sửa phiếu mượn đang trong trạng thái mượn.');
        }
        
        $phieuMuon->load('chiTietPhieuMuons.sach');
        $docGias = DocGia::where('trang_thai', true)->get();
        
        return view('admin.phieu_muon.edit', compact('phieuMuon', 'docGias'));
    }

    public function update(Request $request, PhieuMuon $phieuMuon)
    {
        if ($phieuMuon->trang_thai !== 'đang mượn') {
            return redirect()->route('phieu-muon.index')->with('error', 'Chỉ có thể chỉnh sửa phiếu mượn đang trong trạng thái mượn.');
        }
        
        $request->validate([
            'ngay_hen_tra' => 'required|date|after_or_equal:ngay_muon',
            'ghi_chu' => 'nullable|string',
        ]);
        
        $phieuMuon->update([
            'ngay_hen_tra' => $request->ngay_hen_tra,
            'ghi_chu' => $request->ghi_chu,
        ]);
        
        return redirect()->route('phieu-muon.index')->with('success', 'Cập nhật phiếu mượn thành công!');
    }

    public function destroy(PhieuMuon $phieuMuon)
    {
        if ($phieuMuon->trang_thai === 'đang mượn') {
            return redirect()->route('phieu-muon.index')->with('error', 'Không thể xóa phiếu mượn đang trong trạng thái mượn.');
        }
        
        DB::beginTransaction();
        
        try {
            // Xóa chi tiết phiếu mượn
            $phieuMuon->chiTietPhieuMuons()->delete();
            
            // Xóa phiếu mượn
            $phieuMuon->delete();
            
            DB::commit();
            
            return redirect()->route('phieu-muon.index')->with('success', 'Xóa phiếu mượn thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function returnBook(PhieuMuon $phieuMuon)
    {
        if ($phieuMuon->trang_thai !== 'đang mượn') {
            return redirect()->route('phieu-muon.index')->with('error', 'Phiếu mượn này không ở trạng thái đang mượn.');
        }
        
        $phieuMuon->load('chiTietPhieuMuons.sach');
        
        return view('admin.phieu_muon.return', compact('phieuMuon'));
    }

    public function processReturn(Request $request, PhieuMuon $phieuMuon)
    {
        if ($phieuMuon->trang_thai !== 'đang mượn') {
            return redirect()->route('phieu-muon.index')->with('error', 'Phiếu mượn này không ở trạng thái đang mượn.');
        }
        
        $request->validate([
            'ngay_tra' => 'required|date',
            'tinh_trang_khi_tras' => 'required|array',
            'tinh_trang_khi_tras.*' => 'string',
            'tien_phats' => 'required|array',
            'tien_phats.*' => 'numeric|min:0',
            'ghi_chus' => 'nullable|array',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Cập nhật phiếu mượn
            $phieuMuon->update([
                'ngay_tra' => $request->ngay_tra,
                'trang_thai' => 'đã trả',
            ]);
            
            // Cập nhật chi tiết phiếu mượn
            foreach ($phieuMuon->chiTietPhieuMuons as $chiTietPhieuMuon) {
                $chiTietPhieuMuon->update([
                    'tinh_trang_khi_tra' => $request->tinh_trang_khi_tras[$chiTietPhieuMuon->id],
                    'tien_phat' => $request->tien_phats[$chiTietPhieuMuon->id],
                    'ghi_chu' => $request->ghi_chus[$chiTietPhieuMuon->id] ?? null,
                ]);
                
                // Cập nhật số lượng sách còn lại
                $sach = $chiTietPhieuMuon->sach;
                $sach->so_luong_con_lai += $chiTietPhieuMuon->so_luong;
                $sach->save();
            }
            
            DB::commit();
            
            return redirect()->route('phieu-muon.index')->with('success', 'Xử lý trả sách thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}