<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Grade;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Mengambil ID role berdasarkan nama role
        $adminRole = Role::where('role_name', 'admin')->firstOrFail();
        $staffRole = Role::where('role_name', 'staff')->firstOrFail();
        $borrowerRole = Role::where('role_name', 'borrower')->firstOrFail();

        // Mengambil grade untuk borrower
        $grade1 = Grade::where('grade_name', 'XII RPL 1')->first();
        $grade2 = Grade::where('grade_name', 'XII RPL 2')->first();

        // Menambahkan user admin
        User::create([
            'name'       => 'Admin',
            'username'   => 'admin',
            'email'      => 'admin@gmail.com',
            'phone_number' => '081234567890',
            'password'   => Hash::make('admin123'),
            'role_id'    => $adminRole->id,
            'grade_id'   => null,
            'created_by' => null,
        ]);

        // Menambahkan user staff
        User::create([
            'name'       => 'Staff',
            'username'   => 'staff',
            'email'      => 'staff@gmail.com',
            'phone_number' => '082345678901',
            'password'   => Hash::make('staff123'),
            'role_id'    => $staffRole->id,
            'grade_id'   => null,
            'created_by' => null,
        ]);

        // Menambahkan user borrower
        User::create([
            'name'       => 'Borrower',
            'username'   => 'borrower',
            'email'      => 'borrower@gmail.com',
            'phone_number' => '083456789012',
            'password'   => Hash::make('borrower123'),
            'role_id'    => $borrowerRole->id,
            'grade_id'   => $grade1?->id,
            'created_by' => null,
        ]);

        // Menambahkan user tambahan
        User::create([
            'name'       => 'Aziz Han XK',
            'username'   => 'azizhanxk',
            'email'      => 'azizhanxk@gmail.com',
            'phone_number' => '084567890123',
            'password'   => Hash::make('admin123'),
            'role_id'    => $borrowerRole->id,
            'grade_id'   => $grade2?->id,
            'created_by' => null,
        ]);
    }
}
