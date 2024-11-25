<?php

namespace Database\Seeders;

use App\Models\SkillRequirement;
use Illuminate\Database\Seeder;

class SkillRequirementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SkillRequirement::insert([
            ['item' => 'gargonite sword', 'skill' => 'miner', 'level' => 15],
            ['item' => 'iron sword', 'skill' => 'miner', 'level' => 1],
            ['item' => 'iron bar', 'skill' => 'miner', 'level' => 1],
            ['item' => 'iron dagger', 'skill' => 'miner', 'level' => 1],
            ['item' => 'iron shield', 'skill' => 'miner', 'level' => 1],
            ['item' => 'iron platebody', 'skill' => 'miner', 'level' => 1],
            ['item' => 'iron helm', 'skill' => 'miner', 'level' => 1],
            ['item' => 'gargonite bar', 'skill' => 'miner', 'level' => 15],
            ['item' => 'gargonite dagger', 'skill' => 'miner', 'level' => 15],
            ['item' => 'gargonite platebody', 'skill' => 'miner', 'level' => 15],
            ['item' => 'gargonite helm', 'skill' => 'miner', 'level' => 15],
            ['item' => 'gargonite shield', 'skill' => 'miner', 'level' => 15],
            ['item' => 'gargonite arrows', 'skill' => 'miner', 'level' => 15],
            ['item' => 'gargonite platelegs', 'skill' => 'miner', 'level' => 15],
            ['item' => 'steel sword', 'skill' => 'miner', 'level' => 5],
            ['item' => 'steel dagger', 'skill' => 'miner', 'level' => 5],
            ['item' => 'steel platebody', 'skill' => 'miner', 'level' => 5],
            ['item' => 'steel platelegs', 'skill' => 'miner', 'level' => 5],
            ['item' => 'steel bar', 'skill' => 'miner', 'level' => 5],
            ['item' => 'steel shield', 'skill' => 'miner', 'level' => 5],
            ['item' => 'adron bar', 'skill' => 'miner', 'level' => 25],
            ['item' => 'adron helm', 'skill' => 'miner', 'level' => 25],
            ['item' => 'adron platebody', 'skill' => 'miner', 'level' => 25],
            ['item' => 'adron sword', 'skill' => 'miner', 'level' => 25],
            ['item' => 'adron dagger', 'skill' => 'miner', 'level' => 25],
            ['item' => 'adron platelegs', 'skill' => 'miner', 'level' => 25],
            ['item' => 'adron boots', 'skill' => 'miner', 'level' => 25],
            ['item' => 'adron shield', 'skill' => 'miner', 'level' => 25],
            ['item' => 'yeqdon bar', 'skill' => 'miner', 'level' => 32],
            ['item' => 'yeqdon helm', 'skill' => 'miner', 'level' => 32],
            ['item' => 'yeqdon platebody', 'skill' => 'miner', 'level' => 32],
            ['item' => 'yeqdon sword', 'skill' => 'miner', 'level' => 32],
            ['item' => 'yeqdon dagger', 'skill' => 'miner', 'level' => 32],
            ['item' => 'yeqdon shield', 'skill' => 'miner', 'level' => 32],
            ['item' => 'yeqdon platelegs', 'skill' => 'miner', 'level' => 32],
            ['item' => 'yeqdon boots', 'skill' => 'miner', 'level' => 32],
            ['item' => 'frajrite bar', 'skill' => 'miner', 'level' => 40],
            ['item' => 'frajrite helm', 'skill' => 'miner', 'level' => 40],
            ['item' => 'frajrite platebody', 'skill' => 'miner', 'level' => 40],
            ['item' => 'frajrite sword', 'skill' => 'miner', 'level' => 40],
            ['item' => 'frajrite dagger', 'skill' => 'miner', 'level' => 40],
            ['item' => 'frajrite shield', 'skill' => 'miner', 'level' => 40],
            ['item' => 'frajrite platelegs', 'skill' => 'miner', 'level' => 40],
            ['item' => 'frajrite boots', 'skill' => 'miner', 'level' => 40],
            ['item' => 'steel boots', 'skill' => 'miner', 'level' => 5],
            ['item' => 'adron arrows', 'skill' => 'miner', 'level' => 25],
            ['item' => 'frajrite arrows', 'skill' => 'miner', 'level' => 40],
            ['item' => 'yeqdon arrows', 'skill' => 'miner', 'level' => 32],
            ['item' => 'iron boots', 'skill' => 'miner', 'level' => 1],
            ['item' => 'gargonite boots', 'skill' => 'miner', 'level' => 15],
            ['item' => 'iron platelegs', 'skill' => 'miner', 'level' => 1],
            ['item' => 'oak bow', 'skill' => 'miner', 'level' => 1],
            ['item' => 'birch bow', 'skill' => 'miner', 'level' => 25],
            ['item' => 'yew bow', 'skill' => 'miner', 'level' => 40],
            ['item' => 'arrow shaft', 'skill' => 'miner', 'level' => 1],
            ['item' => 'unfinished arrows', 'skill' => 'miner', 'level' => 1],
            ['item' => 'adron throwing knives', 'skill' => 'miner', 'level' => 1],
            ['item' => 'frajrite throwing knives', 'skill' => 'miner', 'level' => 40],
            ['item' => 'yeqdon throwing knives', 'skill' => 'miner', 'level' => 32],
            ['item' => 'wujkin throiwng knives', 'skill' => 'miner', 'level' => 1],
            ['item' => 'iron arrows', 'skill' => 'miner', 'level' => 1],
            ['item' => 'steel arrows', 'skill' => 'miner', 'level' => 1],
            ['item' => 'gargonite throwing knives', 'skill' => 'miner', 'level' => 15],
            ['item' => 'spruce bow', 'skill' => 'miner', 'level' => 13],
            ['item' => 'iron cart', 'skill' => 'trader', 'level' => 1],
            ['item' => 'steel cart', 'skill' => 'trader', 'level' => 5],
            ['item' => 'yeqdon cart', 'skill' => 'trader', 'level' => 20],
            ['item' => 'frajrite cart', 'skill' => 'trader', 'level' => 32],
        ]);
    }
}
