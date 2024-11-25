<?php

namespace Database\Seeders;

use App\Models\EfficiencyUpgrade;
use Illuminate\Database\Seeder;

class EfficiencyUpgradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EfficiencyUpgrade::insert([
            ['level' => 1, 'price' => 100],
            ['level' => 2, 'price' => 200],
            ['level' => 3, 'price' => 300],
            ['level' => 4, 'price' => 400],
            ['level' => 5, 'price' => 500],
            ['level' => 6, 'price' => 600],
            ['level' => 7, 'price' => 700],
            ['level' => 8, 'price' => 800],
            ['level' => 9, 'price' => 1100],
            ['level' => 10, 'price' => 12000],
        ]);
    }
}
