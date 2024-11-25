<?php

namespace Database\Seeders;

use App\Models\Mineral;
use Illuminate\Database\Seeder;

class MineralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Mineral::insert([
            ['mineral_type' => 'adron', 'mineral_ore' => 'adron ore', 'miner_level' => 20, 'experience' => 50, 'time' => 412, 'min_per_period' => 2, 'max_per_period' => 5, 'permit_cost' => 10, 'location' => 'snerpiir', 'id' => 1],
            ['mineral_type' => 'clay', 'mineral_ore' => 'clay ore', 'miner_level' => 10, 'experience' => 15, 'time' => 95, 'min_per_period' => 2, 'max_per_period' => 6, 'permit_cost' => 8, 'location' => 'golbak', 'id' => 2],
            ['mineral_type' => 'frajrite', 'mineral_ore' => 'frajrite ore', 'miner_level' => 40, 'experience' => 70, 'time' => 1600, 'min_per_period' => 1, 'max_per_period' => 3, 'permit_cost' => 30, 'location' => 'snerpiir', 'id' => 3],
            ['mineral_type' => 'gargonite', 'mineral_ore' => 'gargonite ore', 'miner_level' => 15, 'experience' => 20, 'time' => 160, 'min_per_period' => 3, 'max_per_period' => 7, 'permit_cost' => 10, 'location' => 'snerpiir', 'id' => 4],
            ['mineral_type' => 'iron', 'mineral_ore' => 'iron ore', 'miner_level' => 1, 'experience' => 20, 'time' => 30, 'min_per_period' => 2, 'max_per_period' => 6, 'permit_cost' => 5, 'location' => 'golbak', 'id' => 5],
            ['mineral_type' => 'steel', 'mineral_ore' => 'steel ore', 'miner_level' => 5, 'experience' => 10, 'time' => 120, 'min_per_period' => 3, 'max_per_period' => 6, 'permit_cost' => 10, 'location' => 'golbak', 'id' => 6],
            ['mineral_type' => 'yeqdon', 'mineral_ore' => 'yeqdon ore', 'miner_level' => 30, 'experience' => 50, 'time' => 700, 'min_per_period' => 1, 'max_per_period' => 3, 'permit_cost' => 50, 'location' => 'snerpiir', 'id' => 7],
        ]);
    }
}
