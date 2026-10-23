<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sach;
use App\Models\DocGia;
use App\Models\PhieuMuon;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $totalBooks = Sach::count();
        $totalReaders = DocGia::count();
        $totalBorrowings = PhieuMuon::count();
        $totalUsers = User::count();

        $latestBorrowings = PhieuMuon::with(['docGia', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Sửa phần này để không sử dụng biến $sachIds
        $popularBooks = DB::table('chi_tiet_phieu_muon')
            ->select('sach_id', DB::raw('count(*) as total'))
            ->groupBy('sach_id')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get()
            ->map(function ($item) {
                // Tìm sách theo sach_id mà không cần biến $sachIds
                $item->sach = Sach::find($item->sach_id);
                return $item;
            });

        return view('admin.dashboard', compact(
            'totalBooks',
            'totalReaders',
            'totalBorrowings',
            'totalUsers',
            'latestBorrowings',
            'popularBooks'
        ));
    }
}