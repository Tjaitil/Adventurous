<?php

namespace Database\Factories;

use App\Models\Soldier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WarriorsArmory>
 */
class SoldierArmoryFactory extends Factory
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
            'username' => 'tjaitil',
            'warrior_id' => Soldier::factory(),
            'helm' => null,
            'ammunition' => null,
            'ammunition_amount' => 0,
            'body' => null,
            'right_hand' => null,
            'left_hand' => null,
            'legs' => null,
            'boots' => null,
        ];
    }
}
