<?php

namespace App\Enums;

enum ArmoryParts: string
{
    case HELM = 'helm';
    case AMMUNITION = 'ammunition';
    case BODY = 'body';
    case HAND = 'hand';
    case LEFT_HAND = 'left_hand';
    case RIGHT_HAND = 'right_hand';
    case LEGS = 'legs';
    case BOOTS = 'boots';
}
