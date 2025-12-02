<?php

namespace Database\Factories;

use App\Enums\GameLocations;
use App\Models\ConversationTracker;
use App\Models\Diplomacy;
use App\Models\Farmer;
use App\Models\FarmerWorkforce;
use App\Models\Hunger;
use App\Models\Miner;
use App\Models\MinerWorkforce;
use App\Models\Trader;
use App\Models\UserData;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class UserDataFactory extends Factory
{
    protected $model = UserData::class;

    public function withDefaults(): UserDataFactory
    {
        return $this->afterCreating(function (UserData $userData) {
            Hunger::factory()->create([
                'user_id' => $userData->id,
            ]);
            ConversationTracker::factory()->create([
                'user_id' => $userData->id,
            ]);

            Miner::factory()
                ->count(2)
                ->sequence(
                    [
                        'location' => GameLocations::GOLBAK_LOCATION->value,
                    ],
                    [
                        'location' => GameLocations::SNERPIIR_LOCATION->value,

                    ])
                ->create([
                    'user_id' => $userData->id,
                ]);

            MinerWorkforce::factory()->create([
                'user_id' => $userData->id,
            ]);

            Farmer::factory()
                ->count(2)
                ->sequence(
                    [
                        'location' => GameLocations::TOWHAR_LOCATION->value,
                    ],
                    [
                        'location' => GameLocations::KRASNUR_LOCATION->value,
                    ])
                ->create([
                    'user_id' => $userData->id,
                ]);

            FarmerWorkforce::factory()->create([
                'user_id' => $userData->id,
            ]);

            Trader::factory()->create([
                'username' => $userData->username,
                'user_id' => $userData->id,
            ]);

            Diplomacy::factory()->create([
                'username' => $userData->username,
                'user_id' => $userData->id,
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
        $data = [
            'username' => $this->faker->userName,
            'location' => GameLocations::TOWHAR_LOCATION->value,
            'map_location' => '5.7',
            'game_id' => '',
            'session_id' => 0,
            'destination' => '',
            'arrive_time' => Carbon::now(),
            'profiency' => '',
            'horse' => 'none',
            'artefact' => '',
            'hunger' => 100,
            'hunger_date' => Carbon::now(),
            'frajrite_items' => false,
            'wujkin_items' => false,
            'stockpile_max_amount' => 300,
        ];

        return $data;
    }
}
