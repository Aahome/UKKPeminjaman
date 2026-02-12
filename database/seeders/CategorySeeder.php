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
        ]);

        // Menambahkan kategori Hand Tool
        Category::create([
            'category_name'  => 'School Furniture',
            'description'    => '-blblbl',
        ]);

        // Menambahkan kategori Hand Tool
        Category::create([
            'category_name'  => 'Laboratory Equipment',
            'description'    => '-blblbl',
        ]);

        // Menambahkan kategori Hand Tool
        Category::create([
            'category_name'  => 'Props',
            'description'    => '-blblbl',
        ]);

        // Menambahkan kategori Hand Tool
        Category::create([
            'category_name'  => 'Sports Equipment',
            'description'    => '-blblbl',
        ]);

        // Menambahkan kategori Hand Tool
        Category::create([
            'category_name'  => 'Books',
            'description'    => '-blblbl',
        ]);
    }
}
