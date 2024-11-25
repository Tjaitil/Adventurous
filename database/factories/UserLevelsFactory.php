<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserLevels>
 */
class UserLevelsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => 1,
            'username' => $this->faker->userName,
            'user_id' => User::factory(),
            'adventurer_respect' => '0.0',
            'farmer_level' => 1,
            'farmer_xp' => 0,
            'miner_level' => 1,
            'miner_xp' => 0,
            'trader_level' => 1,
            'trader_xp' => 0,
            'warrior_level' => 1,
            'warrior_xp' => 0,
        ];
    }
}
