<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NhaXuatBan;

class NhaXuatBanSeeder extends Seeder
{
    public function run()
    {
        $nhaXuatBans = [
            ['ten_nxb' => 'NXB Trẻ', 'dia_chi' => 'TP.HCM', 'email' => 'nxbtre@gmail.com'],
            ['ten_nxb' => 'NXB Kim Đồng', 'dia_chi' => 'Hà Nội', 'email' => 'kimdong@gmail.com'],
            ['ten_nxb' => 'NXB Giáo dục', 'dia_chi' => 'Hà Nội', 'email' => 'nxbgd@gmail.com'],
            ['ten_nxb' => 'NXB Văn học', 'dia_chi' => 'Hà Nội', 'email' => 'vanhoc@gmail.com'],
            ['ten_nxb' => 'NXB Tổng hợp', 'dia_chi' => 'TP.HCM', 'email' => 'tonghop@gmail.com'],
        ];

        foreach ($nhaXuatBans as $nhaXuatBan) {
            NhaXuatBan::create($nhaXuatBan);
        }
    }
}