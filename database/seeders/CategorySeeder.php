<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;


class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menambahkan data awal kategori alat
        Category::create([
            'category_name'  => 'Electronics',
            'description'    => '-blblbl',
            'location'       => 'Room A1',
            'created_by'     => 1,
        ]);

        // Menambahkan kategori Hand Tool
        Category::create([
            'category_name'  => 'School Furniture',
            'description'    => '-blblbl',
            'location'       => 'Room B1',
            'created_by'     => 1,
        ]);

        // Menambahkan kategori Hand Tool
        Category::create([
            'category_name'  => 'Laboratory Equipment',
            'description'    => '-blblbl',
            'location'       => 'Lab 01',
            'created_by'     => 1,
        ]);

        // Menambahkan kategori Hand Tool
        Category::create([
            'category_name'  => 'Props',
            'description'    => '-blblbl',
            'location'       => 'Room C1',
            'created_by'     => 1,
        ]);

        // Menambahkan kategori Hand Tool
        Category::create([
            'category_name'  => 'Sports Equipment',
            'description'    => '-blblbl',
            'location'       => 'Gym Hall',
            'created_by'     => 1,
        ]);

        // Menambahkan kategori Hand Tool
        Category::create([
            'category_name'  => 'Books',
            'description'    => '-blblbl',
            'location'       => 'Library',
            'created_by'     => 1,
        ]);
    }
}
