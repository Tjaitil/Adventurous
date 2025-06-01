<?php

namespace Database\Factories;

use App\Enums\GameLocations;
use App\Models\TavernRecruitType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TavernRecruit>
 */
class TavernRecruitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $level = rand(1, 5);
        $price = $level * 100;

        return [
            'location' => array_column(GameLocations::cases(), 'value')[rand(0, count(array_column(GameLocations::cases(), 'value')) - 1)],
            'type_id' => TavernRecruitType::all()->random()->id,
            'price' => $price,
            'level' => $level,
            'user_id' => User::factory()->create(),
            'created_at' => now(),
            'updated_at' => now(),
            'recruited_at' => null,
        ];
    }
}
