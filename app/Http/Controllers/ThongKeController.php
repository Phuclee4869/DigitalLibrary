<?php

namespace App\Http\Controllers;

use App\Models\PhieuMuon;
use App\Models\Sach;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\PDF; // Sửa lại cách import PDF

class ThongKeController extends Controller
{
    public function index()
    {
        return view('admin.thong_ke.index');
    }

    public function thongKeMuonThang(Request $request)
    {
        $thang = $request->thang ?? date('m');
        $nam = $request->nam ?? date('Y');

        // Lấy dữ liệu chi tiết
        $thongKe = PhieuMuon::whereMonth('ngay_muon', $thang)
            ->whereYear('ngay_muon', $nam)
            ->with(['chiTietPhieuMuons.sach', 'docGia', 'user'])
            ->get();

        // Thống kê chi tiết
        $thongKeChiTiet = [
            'tong_phieu' => $thongKe->count(),
            'tong_sach_muon' => $thongKe->sum(function ($phieu) {
                return $phieu->chiTietPhieuMuons->sum('so_luong');
            }),
            'phieu_qua_han' => $thongKe->where('trang_thai', 'quá hạn')->count(),
            'phieu_dang_muon' => $thongKe->where('trang_thai', 'đang mượn')->count(),
            'phieu_da_tra' => $thongKe->where('trang_thai', 'đã trả')->count(),
            'tong_tien_phat' => $thongKe->sum(function ($phieu) {
                return $phieu->chiTietPhieuMuons->sum('tien_phat');
            }),

            // Thống kê theo danh mục
            'theo_danh_muc' => $thongKe->flatMap(function ($phieu) {
                return $phieu->chiTietPhieuMuons->map(function ($chiTiet) {
                    return $chiTiet->sach->danhMuc;
                });
            })->groupBy('ten_danh_muc')->map->count(),

            // Độc giả mượn nhiều nhất
            'doc_gia_muon_nhieu' => $thongKe->groupBy('doc_gia_id')
                ->map(function ($phieus) {
                    $docGia = $phieus->first()->docGia;
                    return [
                        'ten' => $docGia->ho_ten,
                        'so_luot_muon' => $phieus->count(),
                        'tong_sach' => $phieus->sum(function ($p) {
                            return $p->chiTietPhieuMuons->sum('so_luong');
                        })
                    ];
                })->sortByDesc('so_luot_muon')->take(5),

            // Sách được mượn nhiều nhất
            'sach_muon_nhieu' => $thongKe->flatMap(function ($phieu) {
                return $phieu->chiTietPhieuMuons;
            })->groupBy('sach_id')->map(function ($chiTiets) {
                $sach = $chiTiets->first()->sach;
                return [
                    'tieu_de' => $sach->tieu_de,
                    'so_luot_muon' => $chiTiets->count(),
                    'tong_so_luong' => $chiTiets->sum('so_luong')
                ];
            })->sortByDesc('so_luot_muon')->take(5)
        ];

        return view('admin.thong_ke.muon_thang', compact(
            'thongKe',
            'thongKeChiTiet',
            'thang',
            'nam'
        ));
    }

    public function xuatPDF(Request $request)
    {
        $thang = $request->thang ?? date('m');
        $nam = $request->nam ?? date('Y');

        // Lấy dữ liệu thống kê giống như phương thức thongKeMuonThang
        $thongKe = PhieuMuon::whereMonth('ngay_muon', $thang)
            ->whereYear('ngay_muon', $nam)
            ->with(['chiTietPhieuMuons.sach', 'docGia', 'user'])
            ->get();

        // Tính toán thống kê chi tiết
        $thongKeChiTiet = [
            'tong_phieu' => $thongKe->count(),
            'tong_sach_muon' => $thongKe->sum(function ($phieu) {
                return $phieu->chiTietPhieuMuons->sum('so_luong');
            }),
            'phieu_qua_han' => $thongKe->where('trang_thai', 'quá hạn')->count(),
            'tong_tien_phat' => $thongKe->sum(function ($phieu) {
                return $phieu->chiTietPhieuMuons->sum('tien_phat');
            }),
            'theo_danh_muc' => $thongKe->flatMap(function ($phieu) {
                return $phieu->chiTietPhieuMuons->map(function ($chiTiet) {
                    return $chiTiet->sach->danhMuc;
                });
            })->groupBy('ten_danh_muc')->map->count(),
            'doc_gia_muon_nhieu' => $thongKe->groupBy('doc_gia_id')
                ->map(function ($phieus) {
                    $docGia = $phieus->first()->docGia;
                    return [
                        'ten' => $docGia->ho_ten,
                        'so_luot_muon' => $phieus->count(),
                        'tong_sach' => $phieus->sum(function ($p) {
                            return $p->chiTietPhieuMuons->sum('so_luong');
                        })
                    ];
                })->sortByDesc('so_luot_muon')->take(5),
            'sach_muon_nhieu' => $thongKe->flatMap(function ($phieu) {
                return $phieu->chiTietPhieuMuons;
            })->groupBy('sach_id')->map(function ($chiTiets) {
                $sach = $chiTiets->first()->sach;
                return [
                    'tieu_de' => $sach->tieu_de,
                    'so_luot_muon' => $chiTiets->count(),
                    'tong_so_luong' => $chiTiets->sum('so_luong')
                ];
            })->sortByDesc('so_luot_muon')->take(5)
        ];

        // Tạo PDF
        $pdf = PDF::loadView('admin.thong_ke.pdf', compact(
            'thongKe',
            'thongKeChiTiet',
            'thang',
            'nam'
        ));

        // Đặt tên file PDF
        $filename = "thong-ke-muon-sach-thang-{$thang}-nam-{$nam}.pdf";

        // Trả về file PDF để tải xuống
        return $pdf->download($filename);
    }
}
