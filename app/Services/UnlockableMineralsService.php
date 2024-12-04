<?php

namespace App\Services;

use App\libs\Response;
use App\Models\UserData;

class UnlockableMineralsService
{
    public $unlockable_minerals_status = [];

    public function __construct(
        private SessionService $sessionService
    ) {
    }

    public function isWujkinItemUnlocked(UserData $UserData): bool
    {
        return $UserData->wujkin_items === true;
    }

    public function isFrajriteItemUnlocked(UserData $UserData): bool
    {
        return $UserData->frajrite_items === true;
    }

    /**
     * Log message if item is not unlocked
     * @param string $mineral
     * 
     * @return Response
     */
    public function logNotUnlockedMineral(string $mineral)
    {
        return Response::addMessage("You need to unlock $mineral items first")->setStatus(400);
    }
}
