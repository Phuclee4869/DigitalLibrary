<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole     = Role::where('name', 'admin')->first();
        $librarianRole = Role::where('name', 'librarian')->first();
        $readerRole    = Role::where('name', 'reader')->first();

        User::updateOrCreate(['email' => 'admin@thuviendoso.test'], [
            'name' => 'Quản trị viên',
            'password' => Hash::make('Admin@123'),
            'role_id' => $adminRole->id,
        ]);

        User::updateOrCreate(['email' => 'librarian@thuviendoso.test'], [
            'name' => 'Biên mục viên',
            'password' => Hash::make('Lib@123'),
            'role_id' => $librarianRole->id,
        ]);

        User::updateOrCreate(['email' => 'reader_a@thuviendoso.test'], [
            'name' => 'Bạn đọc A',
            'password' => Hash::make('Reader@123'),
            'role_id' => $readerRole->id,
        ]);

        User::updateOrCreate(['email' => 'reader_b@thuviendoso.test'], [
            'name' => 'Bạn đọc B',
            'password' => Hash::make('Reader@123'),
            'role_id' => $readerRole->id,
        ]);
    }
}