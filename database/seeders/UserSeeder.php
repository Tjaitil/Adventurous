<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserData;
use App\Models\UserLevels;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->count(1)
            ->has(UserData::factory()
                ->count(1)
                ->withDefaults()
                ->state([
                    'username' => 'tjaitil',
                ]), 'userData')
            ->has(UserLevels::factory()
                ->count(1)
                ->state([
                    'username' => 'tjaitil',
                ]), 'userLevels')
            ->create([
                'username' => 'tjaitil',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
    }
}
