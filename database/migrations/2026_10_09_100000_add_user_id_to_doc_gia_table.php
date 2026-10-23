<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buổi 8 - V3: liên kết hồ sơ độc giả (doc_gia) với tài khoản đăng nhập (users).
     * Đây là căn cứ xác định "chủ sở hữu" của phiếu mượn khi kiểm quyền trên đối tượng.
     * (phieu_muon.user_id là NHÂN VIÊN lập phiếu, không phải độc giả nên không dùng để kiểm sở hữu.)
     * Mỗi tài khoản gắn tối đa một hồ sơ độc giả (unique, cho phép NULL).
     */
    public function up(): void
    {
        if (!Schema::hasColumn('doc_gia', 'user_id')) {
            Schema::table('doc_gia', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('so_dien_thoai');
                $table->unique('user_id', 'uq_doc_gia_user_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('doc_gia', 'user_id')) {
            Schema::table('doc_gia', function (Blueprint $table) {
                $table->dropUnique('uq_doc_gia_user_id');
                $table->dropColumn('user_id');
            });
        }
    }
};
