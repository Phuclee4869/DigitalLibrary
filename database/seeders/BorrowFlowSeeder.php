<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BorrowFlowSeeder extends Seeder
{
    /**
     * Dữ liệu tối thiểu để chạy thử 3 luồng mượn - trả - gia hạn đầu-cuối.
     */
    public function run(): void
    {
        // 1. Tạo tài khoản người dùng
        $accounts = [
            ['admin@gmail.com', 'Admin', 1],
            ['thuthu@gmail.com', 'Thủ thư', 2],
            ['docgia@gmail.com', 'Độc giả', 3],
        ];

        foreach ($accounts as [$email, $name, $role]) {
            User::updateOrCreate(['email' => $email], [
                'name' => $name,
                'password' => bcrypt('123456'),
                'role_id' => $role,
            ]);
        }

        $now = now();

        // 2. Tạo độc giả
        $readers = [
            1 => ['Nguyễn Văn An', 'an@example.com'],
            2 => ['Trần Thị Bình', 'binh@example.com'],
            3 => ['Lê Văn Cường', 'cuong@example.com'],
        ];

        foreach ($readers as $id => [$name, $email]) {
            DB::table('doc_gia')->updateOrInsert(['id' => $id], [
                'ho_ten' => $name,
                'email' => $email,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 3. Tạo sách (có dữ liệu biên)
        $books = [
            1 => ['Lập trình Laravel căn bản', 'Nguyễn Văn Tánh', 1, 10],
            2 => ['Cơ sở dữ liệu MySQL', 'Trần Minh', 1, 5],
            3 => ['Sách chỉ còn 1 cuốn', 'Tác giả biên', 2, 1],
            4 => ['Sách đã hết hàng', 'Tác giả biên', 2, 0],
        ];

        foreach ($books as $id => [$title, $author, $cat, $qty]) {
            DB::table('sach')->updateOrInsert(['id' => $id], [
                'ten_sach' => $title,
                'tac_gia' => $author,
                'category_id' => $cat,
                'so_luong_con_lai' => $qty,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
