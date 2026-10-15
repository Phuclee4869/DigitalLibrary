<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Buổi 9 - V3: dữ liệu khối lượng vừa đủ để thấy khác biệt khi đo (nếu V2 đã nạp dữ liệu lớn thì KHÔNG cần chạy).
 * CHẠY SAU BorrowFlowSeeder và SearchDemoSeeder.
 *
 *   5 000 đầu sách (ID 10001..15000), 300 độc giả (ID 1001..1300),
 *   4 000 phiếu mượn (ID 20001..24000) với 1-5 dòng chi tiết (đã trả hoặc đang mượn), 20 000 dòng nhật ký.
 *
 * Chạy lại sẽ bị bỏ qua nếu dữ liệu đã có (muốn nạp lại: php artisan migrate:fresh rồi nạp từ đầu).
 */
class PerfLoadSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('sach')->where('id', 10001)->exists()) {
            $this->command?->info('Dữ liệu hiệu năng đã có, bỏ qua.');
            return;
        }

        mt_srand(20261013); // dữ liệu giống nhau mỗi lần nạp
        $now   = now();
        $admin = (int) DB::table('users')->where('email', 'admin@gmail.com')->value('id');

        $words   = ['Lập trình', 'Cơ sở dữ liệu', 'Mạng máy tính', 'Trí tuệ nhân tạo', 'An toàn thông tin', 'Kỹ thuật phần mềm', 'Hệ điều hành', 'Toán rời rạc', 'Học máy', 'Phân tích hệ thống'];
        $authors = ['Nguyễn Văn Tánh', 'Trần Minh', 'Lê Hoàng', 'Phạm Thu Hà', 'Đỗ Quang', 'Vũ Thanh', 'Hoàng Mai'];

        // Sách
        $rows = [];
        for ($i = 1; $i <= 5000; $i++) {
            $rows[] = [
                'id' => 10000 + $i,
                'ten_sach' => $words[$i % 10] . ' - tập ' . ($i % 50) . ' (' . $i . ')',
                'tac_gia' => $authors[$i % 7],
                'category_id' => ($i % 12) + 1,
                'so_luong_con_lai' => $i % 9,
                'created_at' => $now, 'updated_at' => $now,
            ];
        }
        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('sach')->insert($chunk);
        }

        // Độc giả
        $rows = [];
        for ($i = 1; $i <= 300; $i++) {
            $rows[] = ['id' => 1000 + $i, 'ho_ten' => 'Độc giả hiệu năng ' . $i, 'email' => "perf{$i}@example.com",
                       'created_at' => $now, 'updated_at' => $now];
        }
        DB::table('doc_gia')->insert($rows);

        // Phiếu mượn + chi tiết
        $tickets = [];
        $items   = [];
        for ($i = 1; $i <= 4000; $i++) {
            $id       = 20000 + $i;
            $borrowed = $now->copy()->subDays(mt_rand(0, 120));
            $due      = $borrowed->copy()->addDays(14);
            $returned = $i % 3 === 0 ? null : $borrowed->copy()->addDays(mt_rand(1, 20));

            $tickets[] = [
                'id' => $id, 'doc_gia_id' => 1001 + ($i % 300), 'user_id' => $admin,
                'ngay_muon' => $borrowed, 'han_tra' => $due, 'ngay_tra' => $returned,
                'trang_thai' => $returned ? 'đã trả' : 'đang mượn', 'so_lan_gia_han' => 0,
                'created_at' => $borrowed, 'updated_at' => $borrowed,
            ];

            for ($k = 0, $n = mt_rand(1, 5); $k < $n; $k++) {
                $items[] = [
                    'phieu_muon_id' => $id, 'sach_id' => 10001 + (($i * 7 + $k * 131) % 5000),
                    'so_luong' => 1, 'tien_pat' => 0, 'created_at' => $borrowed, 'updated_at' => $borrowed,
                ];
            }
        }
        foreach (array_chunk($tickets, 500) as $chunk) {
            DB::table('phieu_muon')->insert($chunk);
        }
        foreach (array_chunk($items, 500) as $chunk) {
            DB::table('chi_tiet_phieu_muon')->insert($chunk);
        }

        // Nhật ký
        $actions = ['auth.login', 'borrow.create', 'borrow.return', 'borrow.renew', 'error.business'];
        $logs = [];
        for ($i = 0; $i < 20000; $i++) {
            $action = $actions[$i % 5];
            $logs[] = [
                'user_id' => $admin, 'hanh_dong' => $action,
                'doi_tuong' => str_starts_with($action, 'borrow') ? 'phieu_muon' : null,
                'doi_tuong_id' => str_starts_with($action, 'borrow') ? 20001 + ($i % 4000) : null,
                'mo_ta' => "[hiệu năng] {$action} lần " . ($i + 1),
                'du_lieu' => null, 'dia_chi_ip' => '127.0.0.1', 'trinh_duyet' => 'seeder',
                'created_at' => $now->copy()->subMinutes(20000 - $i),
            ];
        }
        foreach (array_chunk($logs, 500) as $chunk) {
            DB::table('nhat_ky_he_thong')->insert($chunk);
        }

        $this->command?->info('Đã nạp: 5000 sách, 300 độc giả, 4000 phiếu, ' . count($items) . ' dòng chi tiết, 20000 nhật ký.');
    }
}
