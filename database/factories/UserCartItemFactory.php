<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserCartItem>
 */
class UserCartItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'item_id' => Item::inRandomOrder()->first()?->id ?? 1,
            'amount' => $this->faker->numberBetween(1, 10),
        ];
    }
}
