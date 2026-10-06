<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('phieu_muon', function (Blueprint $table) {
            // Hạn trả (mặc định 14 ngày kể từ ngày mượn, gán khi lập phiếu)
            $table->dateTime('han_tra')->nullable()->after('ngay_muon');
            // Ngày trả thực tế, NULL nghĩa là chưa trả
            $table->dateTime('ngay_tra')->nullable()->after('han_tra');
        });
    }

    public function down(): void
    {
        Schema::table('phieu_muon', function (Blueprint $table) {
            $table->dropColumn(['han_tra', 'ngay_tra']);
        });
    }
};
