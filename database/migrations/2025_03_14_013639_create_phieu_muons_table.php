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
    Schema::create('phieu_muon', function (Blueprint $table) {
        $table->id();
        $table->foreignId('doc_gia_id')->constrained('doc_gia')->onDelete('cascade');
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->date('ngay_muon');
        $table->date('ngay_hen_tra');
        $table->date('ngay_tra')->nullable();
        $table->enum('trang_thai', ['đang mượn', 'đã trả', 'quá hạn'])->default('đang mượn');
        $table->text('ghi_chu')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phieu_muons');
    }
};
