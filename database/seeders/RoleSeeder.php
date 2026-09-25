<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::insertOrIgnore([
            ['name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'librarian', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'reader', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}