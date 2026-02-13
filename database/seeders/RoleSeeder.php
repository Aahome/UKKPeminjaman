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
            ['role_name' => 'admin', 'created_by' => null, 'modified_by' => null, 'created_at' => now(), 'updated_at' => now()],
            ['role_name' => 'staff', 'created_by' => null, 'modified_by' => null, 'created_at' => now(), 'updated_at' => now()],
            ['role_name' => 'borrower', 'created_by' => null, 'modified_by' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
