<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExtendedDatasetSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Dữ liệu chuẩn (Hợp lệ)
        DB::table('books')->insertOrIgnore([
            [
                'id' => 1,
                'title' => 'Lập trình Laravel căn bản',
                'category_id' => 1,
                'author' => 'Nguyễn Văn Tánh',
                'stock' => 5,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        // 2. Dữ liệu biên (Boundary Data) kiểm thử giới hạn hệ thống
        DB::table('books')->insertOrIgnore([
            [
                'id' => 2,
                'title' => 'Sản phẩm biên - Tiêu đề cực dài kiểm thử giới hạn ký tự hệ thống thư viện số',
                'category_id' => 0,
                'author' => 'Tác giả biên',
                'stock' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        // 3. Dữ liệu biên kiểm thử Khóa hàng & Tranh chấp đồng thời
        DB::table('books')->insertOrIgnore([
            [
                'id' => 3,
                'title' => 'Sách kiểm thử tranh chấp đồng thời (Số lượng = 1)',
                'category_id' => 1,
                'author' => 'Nguyễn Văn Thành Hưng',
                'stock' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}