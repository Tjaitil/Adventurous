<?php

namespace Database\Seeders;

use App\Models\MinerPermitCost;
use Illuminate\Database\Seeder;

class MinerPermitCostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MinerPermitCost::insert([
            ['location' => 'golbak', 'permit_cost' => 300, 'permit_amount' => 50],
            ['location' => 'snerpiir', 'permit_cost' => 500, 'permit_amount' => 40],
        ]);
    }
}
