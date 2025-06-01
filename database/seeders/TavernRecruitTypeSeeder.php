<?php

namespace Database\Seeders;

use App\Enums\TavernRecruitTypes;
use App\Models\TavernRecruitType;
use Illuminate\Database\Seeder;

class TavernRecruitTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TavernRecruitType::create([
            'name' => TavernRecruitTypes::RANGED,
        ]);

        TavernRecruitType::create([
            'name' => TavernRecruitTypes::MELEE,
        ]);

        TavernRecruitType::create([
            'name' => TavernRecruitTypes::FARMER,
        ]);

        TavernRecruitType::create([
            'name' => TavernRecruitTypes::MINER,
        ]);
    }
}
