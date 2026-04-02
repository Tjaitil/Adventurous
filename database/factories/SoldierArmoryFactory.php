<?php

namespace Database\Factories;

use App\Models\Soldier;
use App\Models\WarriorsArmory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WarriorsArmory>
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
            'id' => Soldier::factory(),
            'username' => 'tjaitil',
            'warrior_id' => function (array $attributes) {
                return Soldier::find($attributes['user_id'])->warrior_id;
            },
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
