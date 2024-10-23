<?php

namespace App\Enums;

enum WorldChangeType: string
{
    case NEXT_MAP = 'nextMap';
    case TRAVEL = 'travel';
    case RESPAWN = 'respawn';
}
