<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Dữ liệu tối thiểu để V3 chạy thử hai luồng mượn - trả đầu-cuối.
 * Chạy: php artisan db:seed --class=BorrowFlowSeeder
 */
class BorrowFlowSeeder extends Seeder
{
    public function run(): void
    {
        // 1 = Admin, 2 = Thủ thư, 3 = Độc giả (mật khẩu 123456)
        $accounts = [
            ['admin@gmail.com',  'Admin',    1],
            ['thuthu@gmail.com', 'Thủ thư',  2],
            ['docgia@gmail.com', 'Độc giả',  3],
        ];
        foreach ($accounts as [$email, $name, $role]) {
            User::updateOrCreate(['email' => $email], [
                'name' => $name, 'password' => '123456', 'role_id' => $role,
            ]);
        }

        $now = now();

        // Độc giả
        $readers = [
            1 => ['Nguyễn Văn An', 'an@example.com'],
            2 => ['Trần Thị Bình', 'binh@example.com'],
            3 => ['Lê Văn Cường',  'cuong@example.com'],
        ];
        foreach ($readers as $id => [$name, $email]) {
            DB::table('doc_gia')->updateOrInsert(['id' => $id], [
                'ho_ten' => $name, 'email' => $email,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        // Sách: id => [tên, tác giả, danh mục, số lượng còn]
        // Có dữ liệu biên: id 3 chỉ còn 1 cuốn, id 4 đã hết hàng
        $books = [
            1 => ['Lập trình Laravel căn bản', 'Nguyễn Văn Tánh', 1, 10],
            2 => ['Cơ sở dữ liệu MySQL',       'Trần Minh',        1, 5],
            3 => ['Sách chỉ còn một cuốn',     'Tác giả biên',     2, 1],
            4 => ['Sách đã hết hàng',          'Tác giả biên',     2, 0],
        ];
        foreach ($books as $id => [$title, $author, $cat, $qty]) {
            DB::table('sach')->updateOrInsert(['id' => $id], [
                'ten_sach' => $title, 'tac_gia' => $author,
                'category_id' => $cat, 'so_luong_con_lai' => $qty,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }
}
