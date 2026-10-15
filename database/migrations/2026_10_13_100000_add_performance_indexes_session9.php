<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buổi 9 - V3: chỉ mục phục vụ các truy vấn V3 vừa viết lại (phối hợp với V2, người phụ trách chỉ mục).
     * Mỗi chỉ mục chỉ tạo khi CHƯA có, nên chạy được dù V2 đã tạo trước hay sau.
     *
     * Lưu ý SQLite (CSDL của dự án): khóa ngoại KHÔNG tự sinh chỉ mục như MySQL, nên
     * chi_tiet_phieu_muon.phieu_muon_id trước đây không có chỉ mục -> mọi lần đọc dòng chi tiết đều quét toàn bảng.
     */
    public function up(): void
    {
        if (!Schema::hasIndex('chi_tiet_phieu_muon', 'idx_ctpm_phieu_muon')) {
            Schema::table('chi_tiet_phieu_muon', function (Blueprint $table) {
                $table->index('phieu_muon_id', 'idx_ctpm_phieu_muon');   // getTicketItems, tổng sách, đếm đang mượn
            });
        }

        if (!Schema::hasIndex('chi_tiet_phieu_muon', 'idx_ctpm_sach')) {
            Schema::table('chi_tiet_phieu_muon', function (Blueprint $table) {
                $table->index('sach_id', 'idx_ctpm_sach');
            });
        }

        if (!Schema::hasIndex('phieu_muon', 'idx_phieu_muon_doc_gia_tt')) {
            Schema::table('phieu_muon', function (Blueprint $table) {
                $table->index(['doc_gia_id', 'trang_thai'], 'idx_phieu_muon_doc_gia_tt'); // countBooksBorrowing (QT2)
            });
        }
    }

    public function down(): void
    {
        foreach ([
            ['chi_tiet_phieu_muon', 'idx_ctpm_phieu_muon'],
            ['chi_tiet_phieu_muon', 'idx_ctpm_sach'],
            ['phieu_muon', 'idx_phieu_muon_doc_gia_tt'],
        ] as [$tableName, $index]) {
            if (Schema::hasIndex($tableName, $index)) {
                Schema::table($tableName, function (Blueprint $table) use ($index) {
                    $table->dropIndex($index);
                });
            }
        }
    }
};
