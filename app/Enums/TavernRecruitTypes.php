<?php

namespace App\Enums;

enum TavernRecruitTypes: string
{
    case RANGED = 'ranged';
    case MELEE = 'melee';
    case FARMER = 'farmer';
    case MINER = 'miner';
}
