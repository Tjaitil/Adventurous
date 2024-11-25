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
        ArmoryItemsData::insert([
            ['gargonite sword', 3, 0, 15, 20, 30, 150, 'hand', 'melee'],
            ['iron sword', 2, 0, 1, 8, 0, 100, 'hand', 'melee'],
            ['iron bar', 1, 0, 1, 0, 0, 100, 'none', 'all'],
            ['iron dagger', 1, 0, 1, 5, 0, 100, 'hand', 'melee'],
            ['iron shield', 3, 0, 1, 0, 7, 100, 'left_hand', 'all'],
            ['iron platebody', 5, 0, 1, 0, 8, 100, 'body', 'all'],
            ['iron helm', 3, 0, 1, 0, 6, 100, 'helm', 'all'],
            ['gargonite bar', 1, 0, 15, 0, 0, 150, 'none', 'all'],
            ['gargonite dagger', 2, 0, 15, 17, 0, 150, 'hand', 'melee'],
            ['gargonite platebody', 5, 0, 15, 0, 15, 150, 'body', 'all'],
            ['gargonite helm', 3, 0, 15, 0, 11, 150, 'helm', 'all'],
            ['gargonite shield', 3, 0, 15, 0, 11, 150, 'left_hand', 'all'],
            ['gargonite arrows', 1, 0, 15, 11, 0, 25, 'ammunition', 'ranged'],
            ['gargonite platelegs', 3, 0, 15, 0, 11, 150, 'legs', 'all'],
            ['steel sword', 2, 0, 5, 15, 0, 125, 'hand', 'melee'],
            ['steel dagger', 1, 0, 5, 12, 0, 125, 'hand', 'melee'],
            ['steel platebody', 5, 0, 5, 0, 10, 125, 'body', 'all'],
            ['steel platelegs', 3, 0, 5, 0, 8, 125, 'legs', 'all'],
            ['steel bar', 2, 0, 5, 0, 0, 125, 'none', 'all'],
            ['steel shield', 1, 0, 5, 0, 8, 125, 'left_hand', 'all'],
            ['adron bar', 1, 0, 25, 0, 0, 200, 'none', 'all'],
            ['adron helm', 3, 0, 25, 0, 14, 200, 'helm', 'all'],
            ['adron platebody', 5, 0, 25, 0, 18, 200, 'body', 'all'],
            ['adron sword', 2, 0, 25, 25, 0, 200, 'hand', 'melee'],
            ['adron dagger', 1, 0, 25, 23, 0, 200, 'hand', 'melee'],
            ['adron platelegs', 3, 0, 25, 0, 14, 200, 'legs', 'all'],
            ['adron boots', 2, 0, 25, 0, 11, 200, 'boots', 'all'],
            ['adron shield', 3, 0, 25, 0, 14, 200, 'left_hand', 'all'],
            ['yeqdon bar', 1, 0, 32, 0, 0, 225, 'none', 'all'],
            ['yeqdon helm', 1, 0, 32, 0, 0, 225, 'helm', 'all'],
            ['yeqdon platebody', 1, 0, 32, 0, 21, 225, 'body', 'all'],
            ['yeqdon sword', 1, 0, 32, 30, 0, 225, 'hand', 'melee'],
            ['yeqdon dagger', 2, 0, 32, 28, 0, 225, 'hand', 'melee'],
            ['yeqdon shield', 1, 0, 32, 0, 17, 225, 'left_hand', 'all'],
            ['yeqdon platelegs', 1, 0, 32, 0, 17, 225, 'legs', 'all'],
            ['yeqdon boots', 1, 0, 32, 30, 0, 225, 'boots', 'all'],
            ['frajrite bar', 1, 0, 40, 0, 0, 275, 'none', 'all'],
            ['frajrite helm', 1, 0, 40, 0, 25, 275, 'helm', 'all'],
            ['frajrite platebody', 1, 0, 40, 0, 29, 275, 'body', 'all'],
            ['frajrite sword', 1, 0, 40, 44, 0, 275, 'hand', 'melee'],
            ['frajrite dagger', 1, 0, 40, 35, 0, 275, 'hand', 'melee'],
            ['frajrite shield', 1, 0, 40, 0, 25, 275, 'left_hand', 'all'],
            ['frajrite platelegs', 1, 0, 40, 0, 25, 275, 'legs', 'all'],
            ['frajrite boots', 1, 0, 40, 0, 20, 275, 'boots', 'all'],
            ['steel boots', 1, 0, 5, 0, 5, 125, 'boots', 'all'],
            ['adron arrows', 1, 0, 25, 15, 0, 50, 'ammunition', 'ranged'],
            ['frajrite arrows', 1, 0, 40, 25, 0, 100, 'ammunition', 'ranged'],
            ['yeqdon arrows', 1, 0, 32, 20, 0, 75, 'ammunition', 'ranged'],
            ['iron boots', 1, 0, 1, 0, 2, 100, 'boots', 'all'],
            ['gargonite boots', 1, 0, 15, 0, 8, 150, 'boots', 'all'],
            ['iron platelegs', 3, 0, 1, 0, 6, 100, 'legs', 'all'],
            ['oak bow', 0, 2, 1, 10, 0, 10, 'right_hand', 'ranged'],
            ['birch bow', 0, 2, 25, 26, 0, 0, 'hand', 'ranged'],
            ['yew bow', 0, 2, 40, 42, 0, 2000, 'right_hand', 'ranged'],
            ['arrow shaft', 0, 1, 1, 0, 0, 10, 'none', 'all'],
            ['unfinished arrows', 0, 1, 1, 0, 0, 10, 'none', 'ranged'],
            ['adron throwing knives', 1, 0, 1, 12, 0, 75, 'ammunition', 'ranged'],
            ['frajrite throwing knives', 1, 0, 40, 23, 0, 75, 'ammunition', 'ranged'],
            ['yeqdon throwing knives', 1, 0, 32, 18, 0, 75, 'ammunition', 'ranged'],
            ['wujkin throiwng knives', 1, 0, 1, 0, 0, 1, 'ammunition', 'all'],
            ['iron arrows', 1, 0, 1, 5, 0, 75, 'ammunition', 'ranged'],
            ['steel arrows', 1, 0, 1, 8, 0, 50, 'ammunition', 'ranged'],
            ['gargonite throwing knives', 1, 0, 15, 0, 0, 75, 'ammunition', 'ranged'],
            ['spruce bow', 0, 2, 13, 18, 0, 50, 'right_hand', 'ranged'],
        ]);
    }
}
