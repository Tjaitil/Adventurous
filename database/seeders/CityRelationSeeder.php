<?php

namespace Database\Seeders;

use App\Models\CityRelation;
use Illuminate\Database\Seeder;

class CityRelationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CityRelation::insert([
            ['city' => 'hirtam', 'hirtam' => 1.50, 'pvitul' => 1.30, 'khanz' => 0.50, 'ter' => 1.00, 'fansal_plains' => 1.00],
            ['city' => 'pvitul', 'hirtam' => 1.30, 'pvitul' => 1.50, 'khanz' => 0.50, 'ter' => 1.00, 'fansal_plains' => 1.00],
            ['city' => 'khanz', 'hirtam' => 0.50, 'pvitul' => 0.50, 'khanz' => 1.50, 'ter' => 0.20, 'fansal_plains' => 1.00],
            ['city' => 'ter', 'hirtam' => 1.00, 'pvitul' => 1.00, 'khanz' => 0.60, 'ter' => 1.50, 'fansal_plains' => 0.20],
            ['city' => 'fansal_plains', 'hirtam' => 1.00, 'pvitul' => 1.00, 'khanz' => 1.00, 'ter' => 0.20, 'fansal_plains' => 1.50],
        ]);
    }
}
