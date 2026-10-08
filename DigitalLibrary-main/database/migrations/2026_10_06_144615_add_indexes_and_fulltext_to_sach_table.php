<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('books')) {
            // Xóa index cũ nếu đã lỡ tồn tại để tránh lỗi trùng lặp
            try {
                DB::statement('DROP INDEX IF EXISTS idx_books_title');
                DB::statement('DROP INDEX IF EXISTS idx_books_author');
                DB::statement('DROP INDEX IF EXISTS idx_books_cat_stock');
            } catch (\Exception $e) {
                // Bỏ qua nếu CSDL không hỗ trợ DROP INDEX IF EXISTS
            }

            Schema::table('books', function (Blueprint $table) {
                $table->index('title', 'idx_books_title');
                $table->index('author', 'idx_books_author');
                $table->index(['category_id', 'stock'], 'idx_books_cat_stock');
            });
        }
    }

    public function down(): void {
        if (Schema::hasTable('books')) {
            Schema::table('books', function (Blueprint $table) {
                $table->dropIndex('idx_books_title');
                $table->dropIndex('idx_books_author');
                $table->dropIndex('idx_books_cat_stock');
            });
        }
    }
};