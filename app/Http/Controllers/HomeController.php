<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sach;
use App\Models\DanhMuc;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        try {
            $danhMucs = DanhMuc::withCount('sachs')->get();
            $sachs = Sach::with(['danhMuc', 'tacGia', 'nhaXuatBan'])->paginate(8);
            $totalBooks = Sach::count(); // Thêm dòng này
            
            return view('home', compact('sachs', 'danhMucs', 'totalBooks'));
        } catch (\Exception $e) {
            Log::error('Error in HomeController index: ' . $e->getMessage());
            
            return view('home', [
                'sachs' => collect([]),
                'danhMucs' => collect([]),
                'totalBooks' => 0,
                'error' => 'Lỗi hệ thống: ' . $e->getMessage()
            ]);
        }
    }

    public function search(Request $request) 
    {
        $query = $request->input('query');
        
        try {
            $sachs = Sach::where('tieu_de', 'like', "%$query%")
                ->orWhereHas('tacGia', function($q) use ($query) {
                    $q->where('ten_tac_gia', 'like', "%$query%");
                })
                ->paginate(8);
                
            $danhMucs = DanhMuc::withCount('sachs')->get();
            $totalBooks = Sach::count(); // Thêm dòng này
            
            return view('home', compact('sachs', 'danhMucs', 'query', 'totalBooks'));
        } catch (\Exception $e) {
            Log::error('Error in search method: ' . $e->getMessage());
            return view('home', [
                'sachs' => collect([]),
                'danhMucs' => collect([]),
                'totalBooks' => 0, // Thêm dòng này
                'error' => $e->getMessage(),
                'query' => $query
            ]);
        }
    }

    public function filterByCategory($id)
    {
        try {
            $danhMuc = DanhMuc::findOrFail($id);
            $sachs = Sach::where('danh_muc_id', $id)->paginate(8);
            $danhMucs = DanhMuc::withCount('sachs')->get();
            $totalBooks = Sach::count(); // Thêm dòng này
            
            return view('home', compact('sachs', 'danhMucs', 'danhMuc', 'totalBooks')); 
        } catch (\Exception $e) {
            Log::error('Error in filterByCategory method: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Không tìm thấy danh mục này.');
        }
    }

    public function show($id)
    {
        try {
            $sach = Sach::with(['danhMuc', 'tacGia', 'nhaXuatBan'])->findOrFail($id);
            $relatedBooks = Sach::where('danh_muc_id', $sach->danh_muc_id)
                ->where('id', '!=', $sach->id)
                ->take(4)
                ->get();
            $danhMucs = DanhMuc::withCount('sachs')->get(); // Thêm dòng này
            $totalBooks = Sach::count(); // Thêm dòng này
                
            return view('sach.show', compact('sach', 'relatedBooks', 'danhMucs', 'totalBooks'));
        } catch (\Exception $e) {
            Log::error('Error in show method: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Không tìm thấy sách này.');
        }
    }

    public function debug()
    {
        $danhMucs = DanhMuc::all();
        $sachs = Sach::all();
        
        return response()->json([
            'danh_muc_count' => $danhMucs->count(),
            'sach_count' => $sachs->count(),
            'danh_mucs' => $danhMucs,
            'sachs' => $sachs
        ]);
    }
}