<?php

namespace Database\Seeders;

use App\Models\ArmoryItemsData;
use Illuminate\Database\Seeder;

class ArmoryItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = json_decode(file_get_contents(database_path('seeders/data/armoryItemsData.json')), true);

        ArmoryItemsData::insert($items);
    }
}
