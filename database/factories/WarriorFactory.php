<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Warrior;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Warrior>
 */
class WarriorFactory extends Factory
{
    protected $model = Warrior::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => User::factory()->create()->username,
            'warrior_amount' => 2,
            'mission_id' => 0,
            'mission_countdown' => Carbon::now(),
        ];
    }
}
