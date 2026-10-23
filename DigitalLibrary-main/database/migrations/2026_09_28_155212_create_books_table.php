<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('category_id');
            $table->string('author')->nullable();
            
            // Khai báo CHECK (stock >= 0) trực tiếp tại cột tương thích với SQLite
            $table->integer('stock')->default(1)->check('stock >= 0');
            
            $table->timestamps();

            $table->index('title', 'idx_books_title');
            $table->index('category_id', 'idx_books_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};