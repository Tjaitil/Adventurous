<?php

namespace Database\Seeders;

use App\Models\TavernRecruit;
use Illuminate\Database\Seeder;

class TavernRecruitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TavernRecruit::factory()
            ->count(10)
            ->create([
                'user_id' => 1,
            ]);
    }
}
