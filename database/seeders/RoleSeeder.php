<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'ten_vai_tro' => 'Admin',
                'mo_ta' => 'Quản trị viên hệ thống'
            ],
            [
                'ten_vai_tro' => 'Thủ thư',
                'mo_ta' => 'Nhân viên thư viện'
            ],
            [
                'ten_vai_tro' => 'Độc giả',
                'mo_ta' => 'Người đọc sách'
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}