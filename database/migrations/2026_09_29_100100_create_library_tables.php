<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Các bảng phục vụ nghiệp vụ mượn - trả và gia hạn sách:
     * doc_gia, sach, phieu_muon, chi_tiet_phieu_muon
     */
    public function up(): void
    {
        if (!Schema::hasTable('doc_gia')) {
            Schema::create('doc_gia', function (Blueprint $table) {
                $table->id();
                $table->string('ho_ten');
                $table->string('email')->nullable()->unique();
                $table->string('so_dien_thoai', 20)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('sach')) {
            Schema::create('sach', function (Blueprint $table) {
                $table->id();
                $table->string('ten_sach');
                $table->string('tac_gia')->nullable();
                $table->unsignedBigInteger('category_id')->nullable();
                $table->unsignedInteger('so_luong_con_lai')->default(0);
                $table->timestamps();

                $table->index('ten_sach', 'idx_sach_ten_sach');
                $table->index('category_id', 'idx_sach_category_id');
            });
        }

        if (!Schema::hasTable('phieu_muon')) {
            Schema::create('phieu_muon', function (Blueprint $table) {
                $table->id();
                $table->foreignId('doc_gia_id')->constrained('doc_gia');
                $table->foreignId('user_id')->constrained('users');
                $table->dateTime('ngay_muon');
                $table->dateTime('han_tra')->nullable();
                $table->dateTime('ngay_tra')->nullable();
                $table->string('trang_thai', 30)->default('đang mượn');
                $table->unsignedTinyInteger('so_lan_gia_han')->default(0);
                $table->timestamps();

                $table->index('trang_thai', 'idx_phieu_muon_trang_thai');
            });
        }

        if (!Schema::hasTable('chi_tiet_phieu_muon')) {
            Schema::create('chi_tiet_phieu_muon', function (Blueprint $table) {
                $table->id();
                $table->foreignId('phieu_muon_id')->constrained('phieu_muon')->cascadeOnDelete();
                $table->foreignId('sach_id')->constrained('sach');
                $table->unsignedInteger('so_luong');
                $table->decimal('tien_pat', 12, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_phieu_muon');
        Schema::dropIfExists('phieu_muon');
        Schema::dropIfExists('sach');
        Schema::dropIfExists('doc_gia');
    }
};
