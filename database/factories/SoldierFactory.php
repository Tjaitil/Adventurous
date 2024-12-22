<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Warriors>
 */
class SoldierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => 'tjaitil',
            'warrior_id' => $this->faker->randomNumber(2),
            'type' => 'ranged',
            'training_countdown' => $this->faker->dateTime(),
            'is_training' => false,
            'training_type' => 'none',
            'army_mission' => 0,
            'health' => 100,
            'location' => 'tasnobil',
            'is_resting' => false,
            'rest_start' => $this->faker->dateTime(),
            'user_id' => 1,
        ];
    }
}
