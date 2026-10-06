<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SearchDemoSeeder extends Seeder
{
    /**
     * Dữ liệu mẫu cho kiểm thử tìm kiếm / phân trang (chạy SAU BorrowFlowSeeder).
     * - 120 đầu sách (ID 101..220), tên lặp lại nhiều lần để kiểm tra thứ tự ổn định khi trùng giá trị sắp xếp.
     * - 2 sách có ký tự % và _ trong tên để kiểm tra thoát ký tự đại diện của LIKE.
     * - 3 phiếu mượn mẫu (còn hạn / đã trả / quá hạn) và 60 dòng nhật ký.
     */
    public function run(): void
    {
        $now = now();
        $titles = [
            'Lập trình PHP nâng cao', 'Cấu trúc dữ liệu và giải thuật', 'Mạng máy tính',
            'Trí tuệ nhân tạo cơ bản', 'Cơ sở dữ liệu quan hệ', 'Kỹ thuật phần mềm',
            'An toàn thông tin', 'Học máy ứng dụng', 'Phân tích thiết kế hệ thống',
            'Lập trình Web với Laravel', 'Hệ điều hành', 'Toán rời rạc',
        ];
        $authors = ['Nguyễn Văn Tánh', 'Trần Minh', 'Lê Hoàng', 'Phạm Thu Hà', 'Đỗ Quang'];

        for ($i = 0; $i < 120; $i++) {
            DB::table('sach')->updateOrInsert(['id' => 101 + $i], [
                'ten_sach'         => $titles[$i % count($titles)],   // 10 bản trùng tên mỗi nhóm
                'tac_gia'          => $authors[$i % count($authors)],
                'category_id'      => ($i % 4) + 1,
                'so_luong_con_lai' => $i % 7,                         // có cả sách hết hàng (0)
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
        }

        foreach ([221 => 'Giảm 100% chi phí', 222 => 'File_test_01'] as $id => $title) {
            DB::table('sach')->updateOrInsert(['id' => $id], [
                'ten_sach' => $title, 'tac_gia' => 'Ký tự đặc biệt', 'category_id' => 9,
                'so_luong_con_lai' => 3, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        // Phiếu mượn mẫu: còn hạn, đã trả, quá hạn
        $adminId = DB::table('users')->where('email', 'admin@gmail.com')->value('id');
        $tickets = [
            [1001, 1, $now->copy()->subDays(3),  $now->copy()->addDays(11), null,                      'đang mượn', 0],
            [1002, 2, $now->copy()->subDays(20), $now->copy()->subDays(6),  $now->copy()->subDays(7),  'đã trả',    0],
            [1003, 3, $now->copy()->subDays(30), $now->copy()->subDays(16), null,                      'đang mượn', 0],
        ];
        foreach ($tickets as [$id, $reader, $borrowed, $due, $returned, $status, $renew]) {
            DB::table('phieu_muon')->updateOrInsert(['id' => $id], [
                'doc_gia_id' => $reader, 'user_id' => $adminId, 'ngay_muon' => $borrowed,
                'han_tra' => $due, 'ngay_tra' => $returned, 'trang_thai' => $status,
                'so_lan_gia_han' => $renew, 'created_at' => $borrowed, 'updated_at' => $borrowed,
            ]);
        }

        // 60 dòng nhật ký mẫu (xóa dòng mẫu cũ để chạy lại không bị nhân đôi)
        DB::table('nhat_ky_he_thong')->where('mo_ta', 'like', '[mẫu]%')->delete();
        $actions = ['auth.login', 'borrow.create', 'borrow.return', 'borrow.renew', 'error.business'];
        for ($i = 0; $i < 60; $i++) {
            $action = $actions[$i % count($actions)];
            DB::table('nhat_ky_he_thong')->insert([
                'user_id'      => $adminId,
                'hanh_dong'    => $action,
                'doi_tuong'    => str_starts_with($action, 'borrow') ? 'phieu_muon' : null,
                'doi_tuong_id' => str_starts_with($action, 'borrow') ? 1001 + ($i % 3) : null,
                'mo_ta'        => "[mẫu] {$action} lần " . ($i + 1),
                'du_lieu'      => json_encode(['stt' => $i + 1]),
                'dia_chi_ip'   => '127.0.0.1',
                'trinh_duyet'  => 'seeder',
                'created_at'   => $now->copy()->subMinutes(60 - $i),
            ]);
        }
    }
}
