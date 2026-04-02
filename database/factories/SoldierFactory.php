<?php

namespace Database\Factories;

use App\Models\Soldier;
use App\Models\SoldierArmory;
use App\Models\Warriors;
use App\Models\WarriorsLevels;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Soldier>
 */
class SoldierFactory extends Factory
{
    private static $warriorIdCounter = 1;

    public function configure()
    {
        return $this->afterCreating(function (Soldier $Soldier) {
            SoldierArmory::factory()->create([
                'id' => $Soldier->id,
                'username' => $Soldier->username,
                'warrior_id' => $Soldier->warrior_id,
            ]);

            WarriorsLevels::factory()->create([
                'id' => $Soldier->id,
                'username' => $Soldier->username,
                'warrior_id' => $Soldier->warrior_id,
            ]);
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => 'tjaitil',
            'warrior_id' => function (array $attributes) {
                $warriorId = self::$warriorIdCounter++;
                $currentMaxWarriorId = Warriors::where('username', $attributes['username'])->max('warrior_id');

                return max($warriorId, $currentMaxWarriorId) + 1;
            },
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
