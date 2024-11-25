<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trader>
 */
class TraderFactory extends Factory
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
            'username' => $this->faker->userName,
            'trading_countdown' => $this->faker->dateTime,
            'delivered' => 0,
            'cart_amount' => 0,
            'assignment_id' => 0,
            'cart_id' => 1,
        ];
    }
}
