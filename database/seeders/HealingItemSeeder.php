<?php

namespace Database\Seeders;

use App\Models\HealingItem;
use App\Models\HealingItemRequired;
use Illuminate\Database\Seeder;

class HealingItemSeeder extends Seeder
{
    public function run()
    {
        $healingItems = [
            ['item_id' => 12, 'item' => 'bread', 'price' => 1750, 'heal' => 56, 'bakery_item' => 1],
            ['item_id' => 20, 'item' => 'cooked potato', 'price' => 250, 'heal' => 4, 'bakery_item' => 1],
            ['item_id' => 119, 'item' => 'tomato', 'price' => 50, 'heal' => 4, 'bakery_item' => 0],
            ['item_id' => 122, 'item' => 'roasted tomato', 'price' => 250, 'heal' => 5, 'bakery_item' => 1],
            ['item_id' => 125, 'item' => 'cooked carrot', 'price' => 50, 'heal' => 8, 'bakery_item' => 1],
            ['item_id' => 128, 'item' => 'roasted corn', 'price' => 250, 'heal' => 10, 'bakery_item' => 1],
            ['item_id' => 131, 'item' => 'roasted cabbage', 'price' => 250, 'heal' => 15, 'bakery_item' => 1],
            ['item_id' => 133, 'item' => 'cooked beans', 'price' => 250, 'heal' => 41, 'bakery_item' => 1],
            ['item_id' => 134, 'item' => 'vegetable pot', 'price' => 750, 'heal' => 45, 'bakery_item' => 1],
            ['item_id' => 139, 'item' => 'stew', 'price' => 1750, 'heal' => 52, 'bakery_item' => 1],
            ['item_id' => 142, 'item' => 'beef', 'price' => 750, 'heal' => 28, 'bakery_item' => 1],
            ['item_id' => 145, 'item' => 'chicken', 'price' => 750, 'heal' => 26, 'bakery_item' => 1],
            ['item_id' => 147, 'item' => 'pork', 'price' => 750, 'heal' => 23, 'bakery_item' => 1],
            ['item_id' => 148, 'item' => 'spicy chicken', 'price' => 750, 'heal' => 24, 'bakery_item' => 1],
            ['item_id' => 149, 'item' => 'spicy beef', 'price' => 750, 'heal' => 27, 'bakery_item' => 1],
            ['item_id' => 150, 'item' => 'spicy pork', 'price' => 750, 'heal' => 28, 'bakery_item' => 1],
            ['item_id' => 151, 'item' => 'fruit salad', 'price' => 750, 'heal' => 42, 'bakery_item' => 1],
            ['item_id' => 153, 'item' => 'bass', 'price' => 750, 'heal' => 31, 'bakery_item' => 1],
            ['item_id' => 155, 'item' => 'hornfish', 'price' => 1750, 'heal' => 34, 'bakery_item' => 1],
            ['item_id' => 157, 'item' => 'salmon', 'price' => 750, 'heal' => 42, 'bakery_item' => 1],
            ['item_id' => 159, 'item' => 'lobster', 'price' => 750, 'heal' => 36, 'bakery_item' => 1],
            ['item_id' => 161, 'item' => 'ent\'a', 'price' => 1750, 'heal' => 52, 'bakery_item' => 1],
            ['item_id' => 162, 'item' => 'apple pie', 'price' => 1750, 'heal' => 49, 'bakery_item' => 1],
            ['item_id' => 167, 'item' => 'apple', 'price' => 0, 'heal' => 22, 'bakery_item' => 0],
            ['item_id' => 169, 'item' => 'oranges', 'price' => 0, 'heal' => 13, 'bakery_item' => 0],
            ['item_id' => 170, 'item' => 'watermelon', 'price' => 0, 'heal' => 41, 'bakery_item' => 0],
            ['item_id' => 219, 'item' => 'yest-herb', 'price' => 0, 'heal' => 25, 'bakery_item' => 0],
            ['item_id' => 220, 'item' => 'yas-herb', 'price' => 35, 'heal' => 35, 'bakery_item' => 1],
            ['item_id' => 221, 'item' => 'healing potion', 'price' => 100, 'heal' => 45, 'bakery_item' => 0],
        ];

        HealingItem::factory()->createMany($healingItems);

        $healing_items_required = [
            ['item_id' => 20, 'required_item' => 'potato', 'amount' => 1, 'id' => 1],
            ['item_id' => 12, 'required_item' => 'wheat', 'amount' => 2, 'id' => 2],
            ['item_id' => 12, 'required_item' => 'sugar', 'amount' => 1, 'id' => 3],
            ['item_id' => 122, 'required_item' => 'tomato', 'amount' => 1, 'id' => 4],
            ['item_id' => 125, 'required_item' => 'carrot', 'amount' => 1, 'id' => 5],
            ['item_id' => 128, 'required_item' => 'corn', 'amount' => 1, 'id' => 6],
            ['item_id' => 131, 'required_item' => 'cabbage', 'amount' => 1, 'id' => 7],
            ['item_id' => 133, 'required_item' => 'beans', 'amount' => 1, 'id' => 8],
            ['item_id' => 139, 'required_item' => 'raw beef', 'amount' => 1, 'id' => 9],
            ['item_id' => 139, 'required_item' => 'tomato', 'amount' => 3, 'id' => 10],
            ['item_id' => 148, 'required_item' => 'spices', 'amount' => 1, 'id' => 11],
            ['item_id' => 148, 'required_item' => 'raw chicken', 'amount' => 1, 'id' => 12],
            ['item_id' => 149, 'required_item' => 'spices', 'amount' => 1, 'id' => 13],
            ['item_id' => 149, 'required_item' => 'raw beef', 'amount' => 1, 'id' => 14],
            ['item_id' => 150, 'required_item' => 'spices', 'amount' => 1, 'id' => 15],
            ['item_id' => 150, 'required_item' => 'raw pork', 'amount' => 1, 'id' => 16],
            ['item_id' => 151, 'required_item' => 'apple', 'amount' => 1, 'id' => 17],
            ['item_id' => 151, 'required_item' => 'oranges', 'amount' => 1, 'id' => 18],
            ['item_id' => 151, 'required_item' => 'watermelon', 'amount' => 1, 'id' => 19],
            ['item_id' => 162, 'required_item' => 'apple', 'amount' => 1, 'id' => 20],
            ['item_id' => 162, 'required_item' => 'sugar', 'amount' => 1, 'id' => 21],
            ['item_id' => 162, 'required_item' => 'wheat', 'amount' => 1, 'id' => 22],
            ['item_id' => 153, 'required_item' => 'raw bass', 'amount' => 1, 'id' => 23],
            ['item_id' => 155, 'required_item' => 'raw hornfish', 'amount' => 1, 'id' => 24],
            ['item_id' => 157, 'required_item' => 'raw salmon', 'amount' => 1, 'id' => 25],
            ['item_id' => 159, 'required_item' => 'raw lobster', 'amount' => 1, 'id' => 26],
            ['item_id' => 161, 'required_item' => 'raw ent\'a', 'amount' => 1, 'id' => 27],
            ['item_id' => 142, 'required_item' => 'raw beef', 'amount' => 1, 'id' => 28],
            ['item_id' => 145, 'required_item' => 'raw chicken', 'amount' => 1, 'id' => 29],
            ['item_id' => 147, 'required_item' => 'raw pork', 'amount' => 1, 'id' => 30],
            ['item_id' => 134, 'required_item' => 'cabbage', 'amount' => 1, 'id' => 31],
            ['item_id' => 134, 'required_item' => 'potato', 'amount' => 1, 'id' => 32],
            ['item_id' => 134, 'required_item' => 'corn', 'amount' => 1, 'id' => 33],
            ['item_id' => 134, 'required_item' => 'carrot', 'amount' => 1, 'id' => 34],
        ];

        HealingItemRequired::insert($healing_items_required);
    }
}
