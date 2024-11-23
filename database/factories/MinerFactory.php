<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Miner>
 */
class MinerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => $this->faker->userName,
            'mineral_ore' => null,
            'mining_finishes_at' => Carbon::now(),
            'permits' => 50,
            'location' => 'golbak',
            'user_id' => User::factory(),
        ];
    }
}
