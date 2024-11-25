<?php

namespace Database\Seeders;

use App\Enums\GameLocations;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MineSkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('miner')->insert([
            'username' => 'tjaitil',
            'mineral_ore' => null,
            'mining_finishes_at' => now(),
            'permits' => 1,
            'location' => GameLocations::GOLBAK_LOCATION->value,
            'user_id' => 1,
        ]);

        DB::table('miner')->insert([
            'username' => 'tjaitil',
            'mineral_ore' => null,
            'mining_finishes_at' => now(),
            'permits' => 1,
            'location' => GameLocations::SNERPIIR_LOCATION->value,
            'user_id' => 1,
        ]);

        DB::table('miner_workforce')->insert([
            'username' => 'tjaitil',
            'workforce_total' => 1,
            'avail_workforce' => 1,
            'golbak' => 0,
            'snerpiir' => 0,
            'mineral_quant_level' => 1,
            'user_id' => 1,
        ]);

        DB::table('miner_permit_cost')->insert([
            'location' => GameLocations::GOLBAK_LOCATION->value,
            'permit_cost' => 300,
            'permit_amount' => 50,
        ]);

        DB::table('miner_permit_cost')->insert([
            'location' => GameLocations::SNERPIIR_LOCATION->value,
            'permit_cost' => 500,
            'permit_amount' => 40,
        ]);
    }
}
