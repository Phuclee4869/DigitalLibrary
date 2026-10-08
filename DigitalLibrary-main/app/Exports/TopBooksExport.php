<?php

namespace App\Exports;

use App\Repositories\ReportRepository;

class TopBooksExport
{
    protected $repo;

    public function __construct(ReportRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Xuất toàn bộ báo cáo hệ thống (bao gồm cả 3 báo cáo) ra stream CSV/Excel
     */
    public function exportCsvResponse()
    {
        $fileName = 'bao_cao_tong_hop_thu_vien.csv';
        
        // Lấy dữ liệu cho cả 3 báo cáo
        $summary = $this->repo->getSummary();
        $topBooks = $this->repo->getTopBorrowedBooks(100);
        $monthlyStats = $this->repo->getMonthlyBorrowAndFines();

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($summary, $topBooks, $monthlyStats) {
            $file = fopen('php://output', 'w');
            
            // Thêm UTF-8 BOM để Excel hiển thị đúng tiếng Việt
            fputs($file, "\xEF\xBB\xBF");

            // --- BÁO CÁO 1: THỐNG KÊ TỔNG QUAN ---
            fputcsv($file, ['=== 1. BAO CAO THONG KE TONG QUAN ===']);
            fputcsv($file, ['Chi So', 'Gia Tri']);
            fputcsv($file, ['Tong dau sach', $summary->tong_so_sach]);
            fputcsv($file, ['Sach con trong kho', $summary->tong_sach_con_lai]);
            fputcsv($file, ['Tong doc gia', $summary->tong_doc_gia]);
            fputcsv($file, ['Phieu dang muon', $summary->tong_phieu_dang_muon]);
            fputcsv($file, []); // Dòng trống ngăn cách

            // --- BÁO CÁO 2: TOP SÁCH MƯỢN NHIỀU NHẤT ---
            fputcsv($file, ['=== 2. BAO CAO TOP SACH MUON NHIEU NHAT ===']);
            fputcsv($file, ['ID Sach', 'Ten Sach', 'Tac Gia', 'Danh Muc', 'Tong Luot Muon']);
            foreach ($topBooks as $book) {
                fputcsv($file, [
                    $book->id ?? '',
                    $book->ten_sach ?? '',
                    $book->tac_gia ?? 'N/A',
                    $book->ten_danh_muc ?? 'Chưa phân loại',
                    $book->tong_luot_muon ?? 0
                ]);
            }
            fputcsv($file, []); // Dòng trống ngăn cách

            // --- BÁO CÁO 3: THỐNG KÊ MƯỢN TRẢ & TIỀN PHẠT THEO THÁNG ---
            fputcsv($file, ['=== 3. BAO CAO MUON TRA & TIEN PHAT THEO THANG ===']);
            fputcsv($file, ['Thang (YYYY-MM)', 'Tong Phieu Muon', 'Tong Tien Phat (VND)']);
            foreach ($monthlyStats as $stat) {
                fputcsv($file, [
                    $stat->thang,
                    $stat->tong_phieu_muon,
                    $stat->tong_tien_phat
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}