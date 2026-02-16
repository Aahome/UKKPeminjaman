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
        // Menambahkan data alat kategori Electronics
        Tool::create([
            'tool_name'   => 'Laptop',
            'category_id' => 1,
            'price'       => 8500000.00,
            'stock'       => 67,
            'condition'   => 'good',
            'created_by'  => 1,
        ]);

        // Menambahkan data alat kategori School Furniture
        Tool::create([
            'tool_name'   => 'Wooden Desk',
            'category_id' => 2,
            'price'       => 450000.00,
            'stock'       => 45,
            'condition'   => 'good',
            'created_by'  => 1,
        ]);

        // Menambahkan data alat kategori Laboratory Equipment
        Tool::create([
            'tool_name'   => 'Microscope',
            'category_id' => 3,
            'price'       => 3500000.00,
            'stock'       => 12,
            'condition'   => 'good',
            'created_by'  => 1,
        ]);

        // Menambahkan data alat kategori Props
        Tool::create([
            'tool_name'   => 'Theater Props Set',
            'category_id' => 4,
            'price'       => 750000.00,
            'stock'       => 8,
            'condition'   => 'good',
            'created_by'  => 1,
        ]);

        // Menambahkan data alat kategori Sports Equipment
        Tool::create([
            'tool_name'   => 'Basketball',
            'category_id' => 5,
            'price'       => 350000.00,
            'stock'       => 25,
            'condition'   => 'good',
            'created_by'  => 1,
        ]);
    }
}
