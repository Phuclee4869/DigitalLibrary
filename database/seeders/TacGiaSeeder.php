<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TacGia;

class TacGiaSeeder extends Seeder
{
    public function run()
    {
        $tacGias = [
            ['ten_tac_gia' => 'Nguyễn Nhật Ánh', 'tieu_su' => 'Nhà văn nổi tiếng Việt Nam'],
            ['ten_tac_gia' => 'Tô Hoài', 'tieu_su' => 'Tác giả của Dế Mèn phiêu lưu ký'],
            ['ten_tac_gia' => 'Nam Cao', 'tieu_su' => 'Nhà văn hiện thực Việt Nam'],
            ['ten_tac_gia' => 'Paulo Coelho', 'tieu_su' => 'Nhà văn người Brazil'],
            ['ten_tac_gia' => 'Haruki Murakami', 'tieu_su' => 'Nhà văn Nhật Bản'],
            ['ten_tac_gia' => 'J.K. Rowling', 'tieu_su' => 'Tác giả Harry Potter'],
            ['ten_tac_gia' => 'Dale Carnegie', 'tieu_su' => 'Tác giả sách về kỹ năng'],
        ];

        foreach ($tacGias as $tacGia) {
            TacGia::create($tacGia);
        }
    }
}