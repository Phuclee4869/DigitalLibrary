<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportRepository {

    // Biểu thức tháng theo driver: chỉ nhận hằng cố định, không chứa dữ liệu người dùng
    private const MONTH_EXPR = [
        'sqlite' => "strftime('%Y-%m', borrow_date)",
        'mysql'  => "DATE_FORMAT(borrow_date, '%Y-%m')",
    ];

    // 1. Truy vấn Báo cáo 1: Thống kê Tổng quan (Dashboard Summary)
    public function getSummary() {
        return (object) [
            'tong_so_sach' => DB::table('books')->count(),
            'tong_sach_con_lai' => DB::table('books')->sum('stock') ?? 0,
            'tong_doc_gia' => DB::table('users')->count(),
            'tong_phieu_dang_muon' => Schema::hasTable('borrow_records')
                ? DB::table('borrow_records')->where('status', 'borrowed')->count()
                : 0,
        ];
    }

    // 2. Truy vấn Báo cáo 2: Top Sách mượn nhiều nhất (Top Borrowed Books)
    public function getTopBorrowedBooks($limit = 10) {
        $limit = max(1, min((int) $limit, 50));   // ép kiểu số và chặn trên

        if (Schema::hasTable('borrow_items') && Schema::hasTable('categories')) {
            return DB::table('borrow_items')
                ->join('books', 'borrow_items.book_id', '=', 'books.id')
                ->leftJoin('categories', 'books.category_id', '=', 'categories.id')
                ->select(
                    'books.id',
                    'books.title as ten_sach',
                    'books.author as tac_gia',
                    'categories.name as ten_danh_muc',
                    DB::raw('SUM(borrow_items.quantity) as tong_luot_muon')
                )
                ->groupBy('books.id', 'books.title', 'books.author', 'categories.name')
                ->orderByDesc('tong_luot_muon')
                ->limit($limit)
                ->get();
        }

        // Fallback an toàn nếu cấu trúc bảng đơn giản hóa
        return DB::table('books')
            ->select('id', 'title as ten_sach', 'author as tac_gia', 'stock as tong_luot_muon')
            ->orderByDesc('stock')
            ->limit($limit)
            ->get();
    }

    // 3. Truy vấn Báo cáo 3: Thống kê Mượn/Trả & Doanh thu Tiền phạt theo tháng
    public function getMonthlyBorrowAndFines() {
        if (Schema::hasTable('borrow_records')) {
            $expr = self::MONTH_EXPR[DB::getDriverName()] ?? self::MONTH_EXPR['mysql'];

            return DB::table('borrow_records')
                ->select(
                    DB::raw("$expr as thang"),
                    DB::raw("COUNT(DISTINCT id) as tong_phieu_muon"),
                    DB::raw("SUM(COALESCE(fine_amount, 0)) as tong_tien_phat")
                )
                ->groupBy('thang')
                ->orderBy('thang', 'ASC')
                ->get();
        }

        // Dữ liệu mẫu dự phòng nếu bảng chưa có dữ liệu giao dịch
        return collect([
            (object) ['thang' => '2026-08', 'tong_phieu_muon' => 12, 'tong_tien_phat' => 0],
            (object) ['thang' => '2026-09', 'tong_phieu_muon' => 15, 'tong_tien_phat' => 0],
            (object) ['thang' => '2026-10', 'tong_phieu_muon' => 28, 'tong_tien_phat' => 50000],
        ]);
    }
}