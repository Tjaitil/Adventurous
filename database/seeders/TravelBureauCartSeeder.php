<?php

namespace Database\Seeders;

use App\Models\TravelBureauCart;
use App\Models\TravelBureauCartRequiredItem;
use Illuminate\Database\Seeder;

class TravelBureauCartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TravelBureauCart::insert([
            [
                'name' => 'iron cart',
                'wheel' => 'iron',
                'wood' => 'birch',
                'store_value' => 50,
                'capasity' => 20,
                'towhar' => 5,
                'golbak' => 0,
                'mineral_amount' => 3,
                'wood_amount' => 12,
                'item_id' => 222,
                'id' => 1,
            ],
            [
                'name' => 'steel cart',
                'wheel' => 'steel',
                'wood' => 'oak',
                'store_value' => 500,
                'capasity' => 50,
                'towhar' => 0,
                'golbak' => 4,
                'mineral_amount' => 5,
                'wood_amount' => 12,
                'item_id' => 223,
                'id' => 2,
            ],
            [
                'name' => 'yeqdon cart',
                'wheel' => 'yeqdon',
                'wood' => 'yew',
                'store_value' => 1150,
                'capasity' => 100,
                'towhar' => 0,
                'golbak' => 0,
                'mineral_amount' => 5,
                'wood_amount' => 12,
                'item_id' => 224,
                'id' => 3,
            ],
            [
                'name' => 'frajrite cart',
                'wheel' => 'frajrite',
                'wood' => 'yew',
                'store_value' => 2000,
                'capasity' => 200,
                'towhar' => 0,
                'golbak' => 0,
                'mineral_amount' => 5,
                'wood_amount' => 12,
                'item_id' => 225,
                'id' => 4,
            ],
        ]);

        TravelBureauCartRequiredItem::insert([
            ['required_item' => 'iron bar', 'amount' => 3, 'item_id' => 222],
            ['required_item' => 'birch logs', 'amount' => 12, 'item_id' => 222],
            ['required_item' => 'steel bar', 'amount' => 7, 'item_id' => 223],
            ['required_item' => 'oak logs', 'amount' => 12, 'item_id' => 223],
            ['required_item' => 'yeqdon bar', 'amount' => 5, 'item_id' => 224],
            ['required_item' => 'yew logs', 'amount' => 15, 'item_id' => 224],
            ['required_item' => 'frajrite bar', 'amount' => 5, 'item_id' => 225],
            ['required_item' => 'yew logs', 'amount' => 15, 'item_id' => 225],
        ]);
    }
}
