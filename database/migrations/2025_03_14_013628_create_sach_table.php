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
    Schema::create('sach', function (Blueprint $table) {
        $table->id();
        $table->string('tieu_de');
        $table->foreignId('danh_muc_id')->constrained('danh_muc')->onDelete('cascade');
        $table->foreignId('tac_gia_id')->constrained('tac_gia')->onDelete('cascade');
        $table->foreignId('nha_xuat_ban_id')->constrained('nha_xuat_ban')->onDelete('cascade');
        $table->string('isbn')->nullable();
        $table->integer('so_trang')->nullable();
        $table->integer('nam_xuat_ban')->nullable();
        $table->text('mo_ta')->nullable();
        $table->string('hinh_anh')->nullable();
        $table->integer('so_luong')->default(0);
        $table->integer('so_luong_con_lai')->default(0);
        $table->decimal('gia', 10, 2)->default(0);
        $table->boolean('active')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saches');
    }
};
