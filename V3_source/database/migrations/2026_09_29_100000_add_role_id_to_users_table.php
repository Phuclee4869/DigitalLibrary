<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bổ sung cột role_id cho bảng users (CheckRole và API đăng nhập đang dùng cột này).
 * 1 = Admin, 2 = Thủ thư, 3 = Độc giả
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedTinyInteger('role_id')->default(3)->after('password');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role_id');
            });
        }
    }
};
