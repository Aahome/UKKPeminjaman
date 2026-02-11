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
    $electronics = Category::where('category_name', 'Electronics')->firstOrFail();
    $handTool    = Category::where('category_name', 'Hand Tool')->firstOrFail();

    // Menambahkan data alat kategori Electronics
    Tool::create([
        'tool_name'   => 'Laptop',
        'category_id' => $electronics->id,
        'stock'       => 67,
        'condition'   => 'good',
    ]);

    // Menambahkan data alat kategori Hand Tool
    Tool::create([
        'tool_name'   => 'Screwdriver',
        'category_id' => $handTool->id,
        'stock'       => 20,
        'condition'   => 'good',
    ]);
}
}

