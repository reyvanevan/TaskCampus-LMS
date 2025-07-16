<?php

namespace Database\Seeders;

use App\Models\Semester;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create current semester (July 2025)
        Semester::create([
            'name' => '2025/2026 - Semester 1',
            'start_date' => '2025-07-01',
            'end_date' => '2025-12-31',
            'is_active' => true,
        ]);

        // Create past semester
        Semester::create([
            'name' => '2024/2025 - Semester 2',
            'start_date' => '2025-01-15',
            'end_date' => '2025-06-30',
            'is_active' => false,
        ]);

        // Create past semester
        Semester::create([
            'name' => '2024/2025 - Semester 1',
            'start_date' => '2024-07-15',
            'end_date' => '2024-12-31',
            'is_active' => false,
        ]);
        
        // Create future semester
        Semester::create([
            'name' => '2025/2026 - Semester 2',
            'start_date' => '2026-01-15',
            'end_date' => '2026-06-30',
            'is_active' => false,
        ]);
    }
}