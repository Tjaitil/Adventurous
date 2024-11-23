<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MinerWorkforce>
 */
class MinerWorkforceFactory extends Factory
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
            'workforce_total' => 1,
            'avail_workforce' => 1,
            'golbak' => 0,
            'snerpiir' => 0,
            'efficiency_level' => 1,
            'mineral_quant_level' => 1,
            'user_id' => User::factory(),
        ];
    }
}
