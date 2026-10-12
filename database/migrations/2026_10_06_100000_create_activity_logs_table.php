<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buổi 7 - V3: Bảng nhật ký hoạt động hệ thống (chỉ ghi thêm, không sửa/xóa)
     * và bổ sung chỉ mục phục vụ tìm kiếm / lọc.
     */
    public function up(): void
    {
        if (!Schema::hasTable('nhat_ky_he_thong')) {
            Schema::create('nhat_ky_he_thong', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('hanh_dong', 50);              // vd: borrow.create, auth.login
                $table->string('doi_tuong', 50)->nullable();  // vd: phieu_muon, sach, users
                $table->unsignedBigInteger('doi_tuong_id')->nullable();
                $table->string('mo_ta', 500)->nullable();
                $table->json('du_lieu')->nullable();          // metadata đã lọc thông tin nhạy cảm
                $table->string('dia_chi_ip', 45)->nullable();
                $table->string('trinh_duyet', 255)->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->index('user_id', 'idx_nk_user');
                $table->index('hanh_dong', 'idx_nk_hanh_dong');
                $table->index(['doi_tuong', 'doi_tuong_id'], 'idx_nk_doi_tuong');
                $table->index('created_at', 'idx_nk_created_at');
            });
        }

        Schema::table('sach', function (Blueprint $table) {
            $table->index('tac_gia', 'idx_sach_tac_gia');
        });

        Schema::table('phieu_muon', function (Blueprint $table) {
            $table->index('doc_gia_id', 'idx_phieu_muon_doc_gia');
            $table->index('han_tra', 'idx_phieu_muon_han_tra');
        });
    }

    public function down(): void
    {
        Schema::table('phieu_muon', function (Blueprint $table) {
            $table->dropIndex('idx_phieu_muon_han_tra');
            $table->dropIndex('idx_phieu_muon_doc_gia');
        });
        Schema::table('sach', function (Blueprint $table) {
            $table->dropIndex('idx_sach_tac_gia');
        });
        Schema::dropIfExists('nhat_ky_he_thong');
    }
};
