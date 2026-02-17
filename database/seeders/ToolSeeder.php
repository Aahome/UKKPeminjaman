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
        // Valid prices: multiples of 1000 or 1500 between 2500-10000
        $validPrices = [3000, 4000, 4500, 5000, 6000, 7000, 7500, 8000, 9000, 10000];

        // Menambahkan data alat kategori Electronics
        Tool::create([
            'tool_name'   => 'Laptop',
            'category_id' => 1,
            'price'       => $validPrices[array_rand($validPrices)],
            'stock'       => 67,
            'condition'   => 'good',
            'created_by'  => 1,
        ]);

        // Menambahkan data alat kategori School Furniture
        Tool::create([
            'tool_name'   => 'Wooden Desk',
            'category_id' => 2,
            'price'       => $validPrices[array_rand($validPrices)],
            'stock'       => 45,
            'condition'   => 'good',
            'created_by'  => 1,
        ]);

        // Menambahkan data alat kategori Laboratory Equipment
        Tool::create([
            'tool_name'   => 'Microscope',
            'category_id' => 3,
            'price'       => $validPrices[array_rand($validPrices)],
            'stock'       => 12,
            'condition'   => 'good',
            'created_by'  => 1,
        ]);

        // Menambahkan data alat kategori Props
        Tool::create([
            'tool_name'   => 'Theater Props Set',
            'category_id' => 4,
            'price'       => $validPrices[array_rand($validPrices)],
            'stock'       => 8,
            'condition'   => 'good',
            'created_by'  => 1,
        ]);

        // Menambahkan data alat kategori Sports Equipment
        Tool::create([
            'tool_name'   => 'Basketball',
            'category_id' => 5,
            'price'       => $validPrices[array_rand($validPrices)],
            'stock'       => 25,
            'condition'   => 'good',
            'created_by'  => 1,
        ]);
    }
}
