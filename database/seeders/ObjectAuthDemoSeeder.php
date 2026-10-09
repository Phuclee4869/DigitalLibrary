<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Buổi 8 - V3: dữ liệu để thử kiểm quyền trên đối tượng.
 * CHẠY SAU BorrowFlowSeeder và SearchDemoSeeder (cần doc_gia 1-3 và phiếu 1001-1003).
 *
 *   Độc giả 1 (docgia@gmail.com)  <-> doc_gia #1 <-> phiếu #1001
 *   Độc giả 2 (docgia2@gmail.com) <-> doc_gia #2 <-> phiếu #1002
 *   doc_gia #3 chưa có tài khoản      <-> phiếu #1003 (không ai ngoài nhân viên xem được)
 */
class ObjectAuthDemoSeeder extends Seeder
{
    public function run(): void
    {
        $reader1 = User::updateOrCreate(['email' => 'docgia@gmail.com'], [
            'name' => 'Độc giả', 'password' => bcrypt('123456'), 'role_id' => 3,
        ]);
        $reader2 = User::updateOrCreate(['email' => 'docgia2@gmail.com'], [
            'name' => 'Độc giả 2', 'password' => bcrypt('123456'), 'role_id' => 3,
        ]);

        DB::table('doc_gia')->where('id', 1)->update(['user_id' => $reader1->id]);
        DB::table('doc_gia')->where('id', 2)->update(['user_id' => $reader2->id]);
        DB::table('doc_gia')->where('id', 3)->update(['user_id' => null]);
    }
}
