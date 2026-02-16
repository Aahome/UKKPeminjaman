<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $grades = [
            // RPL - 6 grades
            'XII RPL 1',
            'XII RPL 2',
            'XI RPL 1',
            'XI RPL 2',
            'X RPL 1',
            'X RPL 2',
            
            // TL - 24 grades
            'XII TL 1',
            'XII TL 2',
            'XII TL 3',
            'XII TL 4',
            'XII TL 5',
            'XII TL 6',
            'XII TL 7',
            'XII TL 8',
            'XI TL 1',
            'XI TL 2',
            'XI TL 3',
            'XI TL 4',
            'XI TL 5',
            'XI TL 6',
            'XI TL 7',
            'XI TL 8',
            'X TL 1',
            'X TL 2',
            'X TL 3',
            'X TL 4',
            'X TL 5',
            'X TL 6',
            'X TL 7',
            'X TL 8',
            
            // APL - 3 grades
            'XII APL 1',
            'XI APL 1',
            'X APL 1',
            
            // DKV - 9 grades
            'XII DKV 1',
            'XII DKV 2',
            'XII DKV 3',
            'XI DKV 1',
            'XI DKV 2',
            'XI DKV 3',
            'X DKV 1',
            'X DKV 2',
            'X DKV 3',
            
            // KI - 3 grades
            'XII KI 1',
            'XI KI 1',
            'X KI 1',
        ];

        foreach ($grades as $gradeName) {
            DB::table('grades')->insert([
                'grade_name' => $gradeName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
