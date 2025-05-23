<?php

namespace Database\Seeders;

use App\Models\SmithyItem;
use App\Models\SmithyItemRequired;
use Illuminate\Database\Seeder;

class SmithyItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SmithyItem::insert([
            ['item_id' => 81, 'item' => 'adron arrows', 'store_value' => 50, 'mineral' => 'adron', 'item_multiplier' => 5],
            ['item_id' => 45, 'item' => 'adron bar', 'store_value' => 200, 'mineral' => 'adron', 'item_multiplier' => 1],
            ['item_id' => 51, 'item' => 'adron boots', 'store_value' => 200, 'mineral' => 'adron', 'item_multiplier' => 1],
            ['item_id' => 49, 'item' => 'adron dagger', 'store_value' => 200, 'mineral' => 'adron', 'item_multiplier' => 1],
            ['item_id' => 46, 'item' => 'adron helm', 'store_value' => 200, 'mineral' => 'adron', 'item_multiplier' => 1],
            ['item_id' => 47, 'item' => 'adron platebody', 'store_value' => 200, 'mineral' => 'adron', 'item_multiplier' => 1],
            ['item_id' => 50, 'item' => 'adron platelegs', 'store_value' => 200, 'mineral' => 'adron', 'item_multiplier' => 1],
            ['item_id' => 52, 'item' => 'adron shield', 'store_value' => 200, 'mineral' => 'adron', 'item_multiplier' => 1],
            ['item_id' => 48, 'item' => 'adron sword', 'store_value' => 200, 'mineral' => 'adron', 'item_multiplier' => 1],
            ['item_id' => 188, 'item' => 'adron throwing knives', 'store_value' => 75, 'mineral' => 'adron', 'item_multiplier' => 5],
            ['item_id' => 82, 'item' => 'frajrite arrows', 'store_value' => 100, 'mineral' => 'frajrite', 'item_multiplier' => 5],
            ['item_id' => 72, 'item' => 'frajrite bar', 'store_value' => 275, 'mineral' => 'frajrite', 'item_multiplier' => 1],
            ['item_id' => 79, 'item' => 'frajrite boots', 'store_value' => 275, 'mineral' => 'frajrite', 'item_multiplier' => 1],
            ['item_id' => 76, 'item' => 'frajrite dagger', 'store_value' => 275, 'mineral' => 'frajrite', 'item_multiplier' => 1],
            ['item_id' => 73, 'item' => 'frajrite helm', 'store_value' => 275, 'mineral' => 'frajrite', 'item_multiplier' => 1],
            ['item_id' => 74, 'item' => 'frajrite platebody', 'store_value' => 275, 'mineral' => 'frajrite', 'item_multiplier' => 1],
            ['item_id' => 78, 'item' => 'frajrite platelegs', 'store_value' => 275, 'mineral' => 'frajrite', 'item_multiplier' => 1],
            ['item_id' => 77, 'item' => 'frajrite shield', 'store_value' => 275, 'mineral' => 'frajrite', 'item_multiplier' => 1],
            ['item_id' => 75, 'item' => 'frajrite sword', 'store_value' => 275, 'mineral' => 'frajrite', 'item_multiplier' => 1],
            ['item_id' => 189, 'item' => 'frajrite throwing knives', 'store_value' => 75, 'mineral' => 'frajrite', 'item_multiplier' => 5],
            ['item_id' => 32, 'item' => 'gargonite arrows', 'store_value' => 25, 'mineral' => 'gargonite', 'item_multiplier' => 5],
            ['item_id' => 27, 'item' => 'gargonite bar', 'store_value' => 150, 'mineral' => 'gargonite', 'item_multiplier' => 1],
            ['item_id' => 107, 'item' => 'gargonite boots', 'store_value' => 150, 'mineral' => 'gargonite', 'item_multiplier' => 1],
            ['item_id' => 28, 'item' => 'gargonite dagger', 'store_value' => 150, 'mineral' => 'gargonite', 'item_multiplier' => 1],
            ['item_id' => 30, 'item' => 'gargonite helm', 'store_value' => 150, 'mineral' => 'gargonite', 'item_multiplier' => 1],
            ['item_id' => 29, 'item' => 'gargonite platebody', 'store_value' => 150, 'mineral' => 'gargonite', 'item_multiplier' => 1],
            ['item_id' => 33, 'item' => 'gargonite platelegs', 'store_value' => 150, 'mineral' => 'gargonite', 'item_multiplier' => 1],
            ['item_id' => 31, 'item' => 'gargonite shield', 'store_value' => 150, 'mineral' => 'gargonite', 'item_multiplier' => 1],
            ['item_id' => 13, 'item' => 'gargonite sword', 'store_value' => 150, 'mineral' => 'gargonite', 'item_multiplier' => 1],
            ['item_id' => 200, 'item' => 'gargonite throwing knives', 'store_value' => 75, 'mineral' => 'gargonite', 'item_multiplier' => 5],
            ['item_id' => 198, 'item' => 'iron arrows', 'store_value' => 75, 'mineral' => 'iron', 'item_multiplier' => 5],
            ['item_id' => 21, 'item' => 'iron bar', 'store_value' => 100, 'mineral' => 'iron', 'item_multiplier' => 1],
            ['item_id' => 106, 'item' => 'iron boots', 'store_value' => 100, 'mineral' => 'iron', 'item_multiplier' => 1],
            ['item_id' => 22, 'item' => 'iron dagger', 'store_value' => 100, 'mineral' => 'iron', 'item_multiplier' => 1],
            ['item_id' => 25, 'item' => 'iron helm', 'store_value' => 100, 'mineral' => 'iron', 'item_multiplier' => 1],
            ['item_id' => 24, 'item' => 'iron platebody', 'store_value' => 100, 'mineral' => 'iron', 'item_multiplier' => 1],
            ['item_id' => 108, 'item' => 'iron platelegs', 'store_value' => 100, 'mineral' => 'iron', 'item_multiplier' => 1],
            ['item_id' => 23, 'item' => 'iron shield', 'store_value' => 100, 'mineral' => 'iron', 'item_multiplier' => 1],
            ['item_id' => 17, 'item' => 'iron sword', 'store_value' => 100, 'mineral' => 'iron', 'item_multiplier' => 1],
            ['item_id' => 199, 'item' => 'steel arrows', 'store_value' => 50, 'mineral' => 'steel', 'item_multiplier' => 5],
            ['item_id' => 38, 'item' => 'steel bar', 'store_value' => 125, 'mineral' => 'steel', 'item_multiplier' => 1],
            ['item_id' => 80, 'item' => 'steel boots', 'store_value' => 125, 'mineral' => 'steel', 'item_multiplier' => 1],
            ['item_id' => 35, 'item' => 'steel dagger', 'store_value' => 125, 'mineral' => 'steel', 'item_multiplier' => 1],
            ['item_id' => 36, 'item' => 'steel platebody', 'store_value' => 125, 'mineral' => 'steel', 'item_multiplier' => 1],
            ['item_id' => 37, 'item' => 'steel platelegs', 'store_value' => 125, 'mineral' => 'steel', 'item_multiplier' => 1],
            ['item_id' => 41, 'item' => 'steel shield', 'store_value' => 125, 'mineral' => 'steel', 'item_multiplier' => 1],
            ['item_id' => 34, 'item' => 'steel sword', 'store_value' => 125, 'mineral' => 'steel', 'item_multiplier' => 1],
            ['item_id' => 102, 'item' => 'yeqdon arrows', 'store_value' => 75, 'mineral' => 'yeqdon', 'item_multiplier' => 5],
            ['item_id' => 54, 'item' => 'yeqdon bar', 'store_value' => 225, 'mineral' => 'yeqdon', 'item_multiplier' => 1],
            ['item_id' => 61, 'item' => 'yeqdon boots', 'store_value' => 225, 'mineral' => 'yeqdon', 'item_multiplier' => 1],
            ['item_id' => 58, 'item' => 'yeqdon dagger', 'store_value' => 225, 'mineral' => 'yeqdon', 'item_multiplier' => 1],
            ['item_id' => 55, 'item' => 'yeqdon helm', 'store_value' => 225, 'mineral' => 'yeqdon', 'item_multiplier' => 1],
            ['item_id' => 56, 'item' => 'yeqdon platebody', 'store_value' => 225, 'mineral' => 'yeqdon', 'item_multiplier' => 1],
            ['item_id' => 60, 'item' => 'yeqdon platelegs', 'store_value' => 225, 'mineral' => 'yeqdon', 'item_multiplier' => 1],
            ['item_id' => 59, 'item' => 'yeqdon shield', 'store_value' => 225, 'mineral' => 'yeqdon', 'item_multiplier' => 1],
            ['item_id' => 57, 'item' => 'yeqdon sword', 'store_value' => 225, 'mineral' => 'yeqdon', 'item_multiplier' => 1],
            ['item_id' => 190, 'item' => 'yeqdon throwing knives', 'store_value' => 75, 'mineral' => 'yeqdon', 'item_multiplier' => 5],
            ['item_id' => 43, 'item' => 'brick', 'store_value' => 200, 'mineral' => 'clay', 'item_multiplier' => 1],
        ]);

        SmithyItemRequired::insert([
            ['item_id' => 81, 'required_item' => 'unfinished arrows', 'amount' => 5],
            ['item_id' => 45, 'required_item' => 'adron ore', 'amount' => 1],
            ['item_id' => 51, 'required_item' => 'adron bar', 'amount' => 2],
            ['item_id' => 49, 'required_item' => 'adron bar', 'amount' => 1],
            ['item_id' => 46, 'required_item' => 'adron bar', 'amount' => 3],
            ['item_id' => 47, 'required_item' => 'adron bar', 'amount' => 5],
            ['item_id' => 50, 'required_item' => 'adron bar', 'amount' => 3],
            ['item_id' => 52, 'required_item' => 'adron bar', 'amount' => 3],
            ['item_id' => 48, 'required_item' => 'adron bar', 'amount' => 2],
            ['item_id' => 188, 'required_item' => 'adron bar', 'amount' => 1],
            ['item_id' => 82, 'required_item' => 'frajrite bar', 'amount' => 1],
            ['item_id' => 82, 'required_item' => 'unfinished arrows', 'amount' => 5],
            ['item_id' => 72, 'required_item' => 'frajrite ore', 'amount' => 1],
            ['item_id' => 79, 'required_item' => 'frajrite bar', 'amount' => 1],
            ['item_id' => 76, 'required_item' => 'frajrite bar', 'amount' => 1],
            ['item_id' => 73, 'required_item' => 'frajrite bar', 'amount' => 1],
            ['item_id' => 74, 'required_item' => 'frajrite bar', 'amount' => 1],
            ['item_id' => 78, 'required_item' => 'frajrite bar', 'amount' => 1],
            ['item_id' => 77, 'required_item' => 'frajrite bar', 'amount' => 1],
            ['item_id' => 75, 'required_item' => 'frajrite bar', 'amount' => 1],
            ['item_id' => 189, 'required_item' => 'frajrite bar', 'amount' => 1],
            ['item_id' => 32, 'required_item' => 'unfinished arrows', 'amount' => 5],
            ['item_id' => 27, 'required_item' => 'gargonite ore', 'amount' => 1],
            ['item_id' => 107, 'required_item' => 'gargonite bar', 'amount' => 1],
            ['item_id' => 28, 'required_item' => 'gargonite bar', 'amount' => 2],
            ['item_id' => 30, 'required_item' => 'gargonite bar', 'amount' => 3],
            ['item_id' => 29, 'required_item' => 'gargonite bar', 'amount' => 5],
            ['item_id' => 33, 'required_item' => 'gargonite bar', 'amount' => 3],
            ['item_id' => 31, 'required_item' => 'gargonite bar', 'amount' => 3],
            ['item_id' => 13, 'required_item' => 'gargonite bar', 'amount' => 3],
            ['item_id' => 200, 'required_item' => 'gargonite bar', 'amount' => 5],
            ['item_id' => 198, 'required_item' => 'unfinished arrows', 'amount' => 5],
            ['item_id' => 21, 'required_item' => 'iron ore', 'amount' => 1],
            ['item_id' => 106, 'required_item' => 'iron bar', 'amount' => 1],
            ['item_id' => 22, 'required_item' => 'iron bar', 'amount' => 1],
            ['item_id' => 25, 'required_item' => 'iron bar', 'amount' => 3],
            ['item_id' => 24, 'required_item' => 'iron bar', 'amount' => 5],
            ['item_id' => 108, 'required_item' => 'iron bar', 'amount' => 3],
            ['item_id' => 23, 'required_item' => 'iron bar', 'amount' => 3],
            ['item_id' => 17, 'required_item' => 'iron bar', 'amount' => 2],
            ['item_id' => 199, 'required_item' => 'steel bar', 'amount' => 1],
            ['item_id' => 199, 'required_item' => 'unfinished arrows', 'amount' => 5],
            ['item_id' => 38, 'required_item' => 'steel ore', 'amount' => 1],
            ['item_id' => 80, 'required_item' => 'steel bar', 'amount' => 1],
            ['item_id' => 35, 'required_item' => 'steel bar', 'amount' => 1],
            ['item_id' => 36, 'required_item' => 'steel bar', 'amount' => 5],
            ['item_id' => 37, 'required_item' => 'steel bar', 'amount' => 3],
            ['item_id' => 41, 'required_item' => 'steel bar', 'amount' => 1],
            ['item_id' => 34, 'required_item' => 'steel bar', 'amount' => 2],
            ['item_id' => 102, 'required_item' => 'yeqdon bar', 'amount' => 1],
            ['item_id' => 102, 'required_item' => 'unfinished arrows', 'amount' => 5],
            ['item_id' => 54, 'required_item' => 'yeqdon bar', 'amount' => 1],
            ['item_id' => 61, 'required_item' => 'yeqdon bar', 'amount' => 1],
            ['item_id' => 58, 'required_item' => 'yeqdon bar', 'amount' => 2],
            ['item_id' => 55, 'required_item' => 'yeqdon bar', 'amount' => 1],
            ['item_id' => 56, 'required_item' => 'yeqdon bar', 'amount' => 5],
            ['item_id' => 60, 'required_item' => 'yeqdon bar', 'amount' => 3],
            ['item_id' => 59, 'required_item' => 'yeqdon bar', 'amount' => 1],
            ['item_id' => 57, 'required_item' => 'yeqdon bar', 'amount' => 1],
            ['item_id' => 190, 'required_item' => 'iron bar', 'amount' => 1],
            ['item_id' => 43, 'required_item' => 'clay ore', 'amount' => 2],
            ['item_id' => 81, 'required_item' => 'iron ore', 'amount' => 1],
            ['item_id' => 32, 'required_item' => 'gargonite ore', 'amount' => 1],
        ]);
    }
}
