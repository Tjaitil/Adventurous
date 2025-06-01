<?php

namespace Database\Seeders;

use App\Models\WarriorsLevelsData;
use Illuminate\Database\Seeder;

class WarriorLevelsDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WarriorsLevelsData::insert([
            ['skill_level' => 1, 'next_level' => 55],
            ['skill_level' => 2, 'next_level' => 145],
            ['skill_level' => 3, 'next_level' => 295],
            ['skill_level' => 4, 'next_level' => 505],
            ['skill_level' => 5, 'next_level' => 775],
            ['skill_level' => 6, 'next_level' => 1105],
            ['skill_level' => 7, 'next_level' => 1495],
            ['skill_level' => 8, 'next_level' => 1945],
            ['skill_level' => 9, 'next_level' => 2455],
            ['skill_level' => 10, 'next_level' => 3025],
            ['skill_level' => 11, 'next_level' => 3655],
            ['skill_level' => 12, 'next_level' => 4345],
            ['skill_level' => 13, 'next_level' => 5095],
            ['skill_level' => 14, 'next_level' => 5905],
            ['skill_level' => 15, 'next_level' => 6775],
            ['skill_level' => 16, 'next_level' => 7705],
            ['skill_level' => 17, 'next_level' => 8695],
        ]);
    }
}
