<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Mengisi data awal role (admin, staff, borrower) ke tabel roles
        Role::insert([
            ['role_name' => 'admin'],
            ['role_name' => 'staff'],
            ['role_name' => 'borrower'],
        ]);
    }
}
