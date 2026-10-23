<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('chi_tiet_phieu_muon', function (Blueprint $table) {
        $table->id();
        $table->foreignId('phieu_muon_id')->constrained('phieu_muon')->onDelete('cascade');
        $table->foreignId('sach_id')->constrained('sach')->onDelete('cascade');
        $table->integer('so_luong');
        $table->string('tinh_trang_khi_muon')->nullable();
        $table->string('tinh_trang_khi_tra')->nullable();
        $table->decimal('tien_phat', 10, 2)->default(0);
        $table->text('ghi_chu')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_phieu_muons');
    }
};
