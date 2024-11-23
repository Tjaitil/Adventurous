<?php

namespace Database\Seeders;

use App\Models\ArcheryShopItem;
use App\Models\ArcheryShopItemsRequired;
use Illuminate\Database\Seeder;

class ArcheryShopItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ArcheryShopItem::insert([
            ['id' => 1, 'item_id' => 113, 'item' => 'arrow shaft', 'item_multiplier' => 15, 'store_value' => 10],
            ['id' => 2, 'item_id' => 114, 'item' => 'unfinished arrows', 'item_multiplier' => 15, 'store_value' => 10],
            ['id' => 3, 'item_id' => 110, 'item' => 'oak bow', 'item_multiplier' => 1, 'store_value' => 1200],
            ['id' => 5, 'item_id' => 202, 'item' => 'spruce bow', 'item_multiplier' => 1, 'store_value' => 50],
            ['id' => 6, 'item_id' => 111, 'item' => 'yew bow', 'item_multiplier' => 1, 'store_value' => 2000],
        ]);

        ArcheryShopItemsRequired::insert(
            $items_required = [
                ['item_id' => 113, 'required_item' => 'oak logs', 'amount' => 1, 'id' => 1],
                ['item_id' => 114, 'required_item' => 'feathers', 'amount' => 1, 'id' => 2],
                ['item_id' => 114, 'required_item' => 'arrow shaft', 'amount' => 1, 'id' => 3],
                ['item_id' => 110, 'required_item' => 'oak logs', 'amount' => 3, 'id' => 4],
                ['item_id' => 109, 'required_item' => 'oak logs', 'amount' => 3, 'id' => 5],
                ['item_id' => 202, 'required_item' => 'spruce logs', 'amount' => 3, 'id' => 6],
                ['item_id' => 111, 'required_item' => 'yew logs', 'amount' => 4, 'id' => 7],
            ]
        );
    }
}
