<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Tool;

class ToolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mengambil kategori berdasarkan nama untuk relasi

        // Menambahkan data alat kategori Electronics
        Tool::create([
            'tool_name'   => 'Laptop',
            'category_id' => 1,
            'stock'       => 67,
            'condition'   => 'good',
            'created_by'  => 1,
        ]);
    }
}
