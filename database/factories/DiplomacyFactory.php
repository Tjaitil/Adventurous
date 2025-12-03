<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Diplomacy>
 */
class DiplomacyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => $this->faker->userName(),
            'hirtam' => $this->faker->randomFloat(1, 0, 1),
            'pvitul' => $this->faker->randomFloat(1, 0, 1),
            'khanz' => $this->faker->randomFloat(1, 0, 1),
            'ter' => $this->faker->randomFloat(1, 0, 1),
            'fansal_plains' => $this->faker->randomFloat(1, 0, 1),
            'user_id' => $this->faker->numberBetween(1, 100),
        ];
    }
}
