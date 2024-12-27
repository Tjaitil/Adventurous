<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ItemSeeder::class);
        $this->call(HealingItemSeeder::class);
        $this->call(LevelDataSeeder::class);
        $this->call(MinerPermitCostSeeder::class);
        $this->call(MineralSeeder::class);
        $this->call(CropSeeder::class);
        $this->call(SmithyItemSeeder::class);
        $this->call(EfficiencyUpgradeSeeder::class);
        $this->call(TravelBureauCartSeeder::class);
        $this->call(SkillRequirementSeeder::class);
        $this->call(ArcheryShopItemSeeder::class);
        $this->call(CityRelationSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ArmoryItemSeeder::class);
    }
}
