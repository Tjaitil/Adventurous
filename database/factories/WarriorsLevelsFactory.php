<?php

namespace Database\Factories;

use App\Models\Soldier;
use App\Models\WarriorsLevels;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WarriorsLevels>
 */
class WarriorsLevelsFactory extends Factory
{
    protected $model = WarriorsLevels::class;

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
            'stamina_level' => 1,
            'stamina_xp' => 0,
            'technique_level' => 1,
            'technique_xp' => 0,
            'precision_level' => 1,
            'precision_xp' => 0,
            'strength_level' => 1,
            'strength_xp' => 0,
        ];
    }
}
