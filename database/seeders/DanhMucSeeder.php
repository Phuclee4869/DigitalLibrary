<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DanhMuc;

class DanhMucSeeder extends Seeder
{
    public function run()
    {
        $danhMucs = [
            ['ten_danh_muc' => 'Văn học Việt Nam', 'mo_ta' => 'Các tác phẩm văn học Việt Nam'],
            ['ten_danh_muc' => 'Văn học nước ngoài', 'mo_ta' => 'Các tác phẩm văn học nước ngoài'],
            ['ten_danh_muc' => 'Sách thiếu nhi', 'mo_ta' => 'Sách dành cho trẻ em'],
            ['ten_danh_muc' => 'Sách giáo khoa', 'mo_ta' => 'Sách học tập'],
            ['ten_danh_muc' => 'Sách kỹ năng sống', 'mo_ta' => 'Sách về kỹ năng sống và phát triển bản thân'],
            ['ten_danh_muc' => 'Sách khoa học', 'mo_ta' => 'Sách về khoa học và công nghệ'],
            ['ten_danh_muc' => 'Truyện tranh', 'mo_ta' => 'Truyện tranh và manga'],
            ['ten_danh_muc' => 'Tiểu thuyết', 'mo_ta' => 'Tiểu thuyết các thể loại'],
        ];

        foreach ($danhMucs as $danhMuc) {
            DanhMuc::create($danhMuc);
        }
    }
}