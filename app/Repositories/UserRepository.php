<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class UserRepository
{
    /** Buổi 8 - Tìm tài khoản theo mã (không trả mật khẩu). */
    public function findById(int $id)
    {
        return DB::table('users')
            ->where('id', $id)
            ->select('id', 'email', 'role_id')
            ->first();
    }
}
