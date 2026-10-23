<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sach;

class SachSeeder extends Seeder
{
    public function run()
    {
        $sachs = [
            [
                'tieu_de' => 'Tôi thấy hoa vàng trên cỏ xanh',
                'danh_muc_id' => 1,
                'tac_gia_id' => 1,
                'nha_xuat_ban_id' => 1,
                'mo_ta' => 'Tác phẩm của Nguyễn Nhật Ánh',
                'so_trang' => 250,
                'nam_xuat_ban' => 2010,
                'gia' => 85000,
                'so_luong' => 10,
                'so_luong_con_lai' => 10,
            ],
            [
                'tieu_de' => 'Dế Mèn phiêu lưu ký',
                'danh_muc_id' => 3,
                'tac_gia_id' => 2,
                'nha_xuat_ban_id' => 2,
                'mo_ta' => 'Tác phẩm của Tô Hoài',
                'so_trang' => 200,
                'nam_xuat_ban' => 1941,
                'gia' => 65000,
                'so_luong' => 15,
                'so_luong_con_lai' => 15,
            ],
            [
                'tieu_de' => 'Nhà giả kim',
                'danh_muc_id' => 2,
                'tac_gia_id' => 4,
                'nha_xuat_ban_id' => 1,
                'mo_ta' => 'Cuốn sách bán chạy của Paulo Coelho',
                'so_trang' => 224,
                'nam_xuat_ban' => 1988,
                'gia' => 78000,
                'so_luong' => 20,
                'so_luong_con_lai' => 20,
            ],
            [
                'tieu_de' => 'Harry Potter và Hòn đá Phù thủy',
                'danh_muc_id' => 8,
                'tac_gia_id' => 6,
                'nha_xuat_ban_id' => 1,
                'mo_ta' => 'Phần đầu tiên trong loạt truyện Harry Potter',
                'so_trang' => 320,
                'nam_xuat_ban' => 1997,
                'gia' => 150000,
                'so_luong' => 25,
                'so_luong_con_lai' => 25,
            ],
            [
                'tieu_de' => 'Đắc nhân tâm',
                'danh_muc_id' => 5,
                'tac_gia_id' => 7,
                'nha_xuat_ban_id' => 5,
                'mo_ta' => 'Sách về nghệ thuật giao tiếp',
                'so_trang' => 291,
                'nam_xuat_ban' => 1936,
                'gia' => 90000,
                'so_luong' => 30,
                'so_luong_con_lai' => 30,
            ],
        ];

        foreach ($sachs as $sach) {
            Sach::create($sach);
        }
    }
}