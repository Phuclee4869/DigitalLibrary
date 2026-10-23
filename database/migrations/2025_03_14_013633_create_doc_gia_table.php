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
    Schema::create('doc_gia', function (Blueprint $table) {
        $table->id();
        $table->string('ho_ten');
        $table->date('ngay_sinh')->nullable();
        $table->text('dia_chi')->nullable();
        $table->string('email')->nullable();
        $table->string('so_dien_thoai')->nullable();
        $table->date('ngay_dang_ky');
        $table->date('ngay_het_han')->nullable();
        $table->boolean('trang_thai')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doc_gias');
    }
};
