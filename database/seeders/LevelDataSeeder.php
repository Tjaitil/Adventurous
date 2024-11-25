<?php

namespace Database\Seeders;

use App\Models\LevelData;
use Illuminate\Database\Seeder;

class LevelDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LevelData::insert([
            ['level' => 1, 'next_Level' => 45, 'max_farm_workers' => 2, 'max_mine_workers' => 2, 'max_warriors' => 2, 'max_efficiency_level' => 1],
            ['level' => 2, 'next_Level' => 105, 'max_farm_workers' => 2, 'max_mine_workers' => 2, 'max_warriors' => 2, 'max_efficiency_level' => 1],
            ['level' => 3, 'next_Level' => 205, 'max_farm_workers' => 3, 'max_mine_workers' => 3, 'max_warriors' => 3, 'max_efficiency_level' => 1],
            ['level' => 4, 'next_Level' => 345, 'max_farm_workers' => 3, 'max_mine_workers' => 3, 'max_warriors' => 3, 'max_efficiency_level' => 1],
            ['level' => 5, 'next_Level' => 525, 'max_farm_workers' => 3, 'max_mine_workers' => 3, 'max_warriors' => 3, 'max_efficiency_level' => 2],
            ['level' => 6, 'next_Level' => 745, 'max_farm_workers' => 4, 'max_mine_workers' => 4, 'max_warriors' => 4, 'max_efficiency_level' => 2],
            ['level' => 7, 'next_Level' => 1005, 'max_farm_workers' => 4, 'max_mine_workers' => 4, 'max_warriors' => 4, 'max_efficiency_level' => 2],
            ['level' => 8, 'next_Level' => 1305, 'max_farm_workers' => 4, 'max_mine_workers' => 4, 'max_warriors' => 5, 'max_efficiency_level' => 2],
            ['level' => 9, 'next_Level' => 1645, 'max_farm_workers' => 4, 'max_mine_workers' => 4, 'max_warriors' => 5, 'max_efficiency_level' => 2],
            ['level' => 10, 'next_Level' => 2025, 'max_farm_workers' => 5, 'max_mine_workers' => 5, 'max_warriors' => 5, 'max_efficiency_level' => 2],
            ['level' => 11, 'next_Level' => 2445, 'max_farm_workers' => 5, 'max_mine_workers' => 5, 'max_warriors' => 6, 'max_efficiency_level' => 2],
            ['level' => 12, 'next_Level' => 2905, 'max_farm_workers' => 5, 'max_mine_workers' => 5, 'max_warriors' => 6, 'max_efficiency_level' => 2],
            ['level' => 13, 'next_Level' => 3405, 'max_farm_workers' => 5, 'max_mine_workers' => 5, 'max_warriors' => 6, 'max_efficiency_level' => 3],
            ['level' => 14, 'next_Level' => 3945, 'max_farm_workers' => 6, 'max_mine_workers' => 6, 'max_warriors' => 6, 'max_efficiency_level' => 3],
            ['level' => 15, 'next_Level' => 4525, 'max_farm_workers' => 6, 'max_mine_workers' => 6, 'max_warriors' => 7, 'max_efficiency_level' => 3],
            ['level' => 16, 'next_Level' => 5145, 'max_farm_workers' => 6, 'max_mine_workers' => 6, 'max_warriors' => 7, 'max_efficiency_level' => 3],
            ['level' => 17, 'next_Level' => 5805, 'max_farm_workers' => 6, 'max_mine_workers' => 6, 'max_warriors' => 7, 'max_efficiency_level' => 3],
            ['level' => 18, 'next_Level' => 6505, 'max_farm_workers' => 7, 'max_mine_workers' => 7, 'max_warriors' => 7, 'max_efficiency_level' => 4],
            ['level' => 19, 'next_Level' => 7245, 'max_farm_workers' => 7, 'max_mine_workers' => 7, 'max_warriors' => 7, 'max_efficiency_level' => 4],
            ['level' => 20, 'next_Level' => 8025, 'max_farm_workers' => 7, 'max_mine_workers' => 7, 'max_warriors' => 7, 'max_efficiency_level' => 4],
            ['level' => 21, 'next_Level' => 8845, 'max_farm_workers' => 7, 'max_mine_workers' => 7, 'max_warriors' => 7, 'max_efficiency_level' => 4],
            ['level' => 22, 'next_Level' => 9705, 'max_farm_workers' => 7, 'max_mine_workers' => 7, 'max_warriors' => 7, 'max_efficiency_level' => 5],
            ['level' => 23, 'next_Level' => 10605, 'max_farm_workers' => 8, 'max_mine_workers' => 8, 'max_warriors' => 8, 'max_efficiency_level' => 5],
            ['level' => 24, 'next_Level' => 11545, 'max_farm_workers' => 8, 'max_mine_workers' => 8, 'max_warriors' => 8, 'max_efficiency_level' => 5],
            ['level' => 25, 'next_Level' => 12525, 'max_farm_workers' => 8, 'max_mine_workers' => 8, 'max_warriors' => 8, 'max_efficiency_level' => 5],
            ['level' => 26, 'next_Level' => 13545, 'max_farm_workers' => 8, 'max_mine_workers' => 8, 'max_warriors' => 8, 'max_efficiency_level' => 6],
            ['level' => 27, 'next_Level' => 14605, 'max_farm_workers' => 8, 'max_mine_workers' => 8, 'max_warriors' => 8, 'max_efficiency_level' => 6],
            ['level' => 28, 'next_Level' => 15705, 'max_farm_workers' => 8, 'max_mine_workers' => 8, 'max_warriors' => 9, 'max_efficiency_level' => 6],
            ['level' => 29, 'next_Level' => 16845, 'max_farm_workers' => 9, 'max_mine_workers' => 9, 'max_warriors' => 9, 'max_efficiency_level' => 6],
            ['level' => 30, 'next_Level' => 18025, 'max_farm_workers' => 9, 'max_mine_workers' => 9, 'max_warriors' => 9, 'max_efficiency_level' => 6],
            ['level' => 31, 'next_Level' => 19245, 'max_farm_workers' => 9, 'max_mine_workers' => 9, 'max_warriors' => 9, 'max_efficiency_level' => 7],
            ['level' => 32, 'next_Level' => 20505, 'max_farm_workers' => 9, 'max_mine_workers' => 9, 'max_warriors' => 10, 'max_efficiency_level' => 7],
            ['level' => 33, 'next_Level' => 21805, 'max_farm_workers' => 9, 'max_mine_workers' => 9, 'max_warriors' => 10, 'max_efficiency_level' => 7],
            ['level' => 34, 'next_Level' => 23145, 'max_farm_workers' => 10, 'max_mine_workers' => 10, 'max_warriors' => 10, 'max_efficiency_level' => 7],
            ['level' => 35, 'next_Level' => 24525, 'max_farm_workers' => 10, 'max_mine_workers' => 10, 'max_warriors' => 10, 'max_efficiency_level' => 7],
            ['level' => 36, 'next_Level' => 25945, 'max_farm_workers' => 10, 'max_mine_workers' => 10, 'max_warriors' => 11, 'max_efficiency_level' => 7],
            ['level' => 37, 'next_Level' => 27405, 'max_farm_workers' => 10, 'max_mine_workers' => 10, 'max_warriors' => 11, 'max_efficiency_level' => 8],
            ['level' => 38, 'next_Level' => 28905, 'max_farm_workers' => 11, 'max_mine_workers' => 11, 'max_warriors' => 11, 'max_efficiency_level' => 8],
            ['level' => 39, 'next_Level' => 30445, 'max_farm_workers' => 11, 'max_mine_workers' => 11, 'max_warriors' => 11, 'max_efficiency_level' => 8],
            ['level' => 40, 'next_Level' => 32025, 'max_farm_workers' => 11, 'max_mine_workers' => 11, 'max_warriors' => 12, 'max_efficiency_level' => 8],
            ['level' => 41, 'next_Level' => 33645, 'max_farm_workers' => 11, 'max_mine_workers' => 11, 'max_warriors' => 12, 'max_efficiency_level' => 9],
            ['level' => 42, 'next_Level' => 35305, 'max_farm_workers' => 11, 'max_mine_workers' => 11, 'max_warriors' => 12, 'max_efficiency_level' => 9],
            ['level' => 43, 'next_Level' => 37005, 'max_farm_workers' => 11, 'max_mine_workers' => 11, 'max_warriors' => 12, 'max_efficiency_level' => 9],
            ['level' => 44, 'next_Level' => 38745, 'max_farm_workers' => 12, 'max_mine_workers' => 12, 'max_warriors' => 13, 'max_efficiency_level' => 10],
            ['level' => 45, 'next_Level' => 40525, 'max_farm_workers' => 0, 'max_mine_workers' => 0, 'max_warriors' => 0, 'max_efficiency_level' => 9],
            ['level' => 46, 'next_Level' => 42345, 'max_farm_workers' => 0, 'max_mine_workers' => 0, 'max_warriors' => 0, 'max_efficiency_level' => 9],
            ['level' => 47, 'next_Level' => 44205, 'max_farm_workers' => 0, 'max_mine_workers' => 0, 'max_warriors' => 0, 'max_efficiency_level' => 9],
            ['level' => 48, 'next_Level' => 46105, 'max_farm_workers' => 0, 'max_mine_workers' => 0, 'max_warriors' => 0, 'max_efficiency_level' => 9],
            ['level' => 49, 'next_Level' => 48045, 'max_farm_workers' => 0, 'max_mine_workers' => 0, 'max_warriors' => 0, 'max_efficiency_level' => 9],
            ['level' => 50, 'next_Level' => 50025, 'max_farm_workers' => 0, 'max_mine_workers' => 0, 'max_warriors' => 0, 'max_efficiency_level' => 10],
        ]);
    }
}
