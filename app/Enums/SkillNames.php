<?php

namespace App\Enums;

enum SkillNames: string
{
    case ADVENTURER = "adventurer";
    case MINER = "miner";
    case FARMER = "farmer";
    case TRADER = "trader";
    case WARRIOR = "warrior";


    /**
     * 
     * @return array 
     */
    public static function getSkillNames()
    {
        return \array_column(self::cases(), 'value');
    }
}
