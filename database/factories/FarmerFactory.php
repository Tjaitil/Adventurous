<?php

namespace Database\Factories;

use App\Enums\GameLocations;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Farmer>
 */
class FarmerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'username' => $this->faker->name,
            'fields' => $this->faker->numberBetween(1, 10),
            'crop_type' => null,
            'crop_quant' => 0,
            'crop_finishes_at' => Carbon::now(),
            'location' => GameLocations::TOWHAR_LOCATION->value,
        ];
    }
}
