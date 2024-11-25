<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FarmerWorkforce>
 */
class FarmerWorkforceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => $this->faker->name,
            'workforce_total' => 3,
            'avail_workforce' => 3,
            'towhar' => 0,
            'krasnur' => 0,
            'efficiency_level' => 1,
        ];
    }
}
