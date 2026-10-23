<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => 1, // Admin
        ]);

        User::create([
            'name' => 'Thủ thư',
            'email' => 'thuthu@example.com',
            'password' => Hash::make('password'),
            'role_id' => 2, // Thủ thư
        ]);
    }
}