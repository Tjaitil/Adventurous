<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Warrior;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class WarriorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Warrior::factory()->count(1)->create(
            [
                'username' => User::factory()->create()->username,
                'warrior_amount' => 0,
                'mission_id' => 0,
                'mission_countdown' => Carbon::now(),
            ]
        );
    }
}
