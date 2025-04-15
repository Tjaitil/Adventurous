<?php

namespace App\Enums;

enum GameEvents: string
{
    case XpGainedEvent = 'XpGainedEvent';
    case InventoryChangedEvent = 'InventoryChangedEvent';
}
