<?php

namespace Database\Seeders;

use App\Models\TraderAssignmentType;
use Illuminate\Database\Seeder;

class TraderAssignmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TraderAssignmentType::insert([[
            'id' => 1,
            'type' => 'small',
            'xp_per_cargo' => 0.2,
            'xp_finished' => 50,
            'xp_started' => 5,
            'item_reward_amount' => 1,
            'required_level' => 1,
            'currency_reward_amount' => 20,
            'diplomacy_percentage' => 0.1,
        ], [
            'id' => 2,
            'type' => 'medium',
            'xp_per_cargo' => 0.7,
            'xp_finished' => 150,
            'xp_started' => 15,
            'item_reward_amount' => 3,
            'required_level' => 10,
            'currency_reward_amount' => 80,
            'diplomacy_percentage' => 0.5,
        ], [
            'id' => 3,
            'type' => 'large',
            'xp_per_cargo' => 1,
            'xp_finished' => 200,
            'xp_started' => 20,
            'item_reward_amount' => 5,
            'required_level' => 15,
            'currency_reward_amount' => 120,
            'diplomacy_percentage' => 0.7,
        ], [
            'id' => 4,
            'type' => TraderAssignmentType::$FAVOR_TYPE,
            'xp_per_cargo' => 0.5,
            'xp_finished' => 100,
            'xp_started' => 10,
            'item_reward_amount' => 2,
            'required_level' => 5,
            'currency_reward_amount' => 50,
            'diplomacy_percentage' => 0.3,
        ]]);
    }
}
