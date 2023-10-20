<?php

namespace App\Services;

use App\libs\Response;
use App\Models\UserData;

class UnlockableMineralsService
{
    public $unlockable_minerals_status = [];

    public function __construct(
        private UserData $userData,
        private SessionService $sessionService
    ) {
        $this->unlockable_minerals_status = $this->userData->select('wujkin_items', 'frajrite_items')
            ->where('username', $sessionService->getCurrentUsername())->first();
    }

    public function isWujkinItemUnlocked(): bool
    {
        return $this->unlockable_minerals_status->wujkin_items === 1;
    }

    public function isFrajriteItemUnlocked(): bool
    {
        return $this->unlockable_minerals_status->frajrite_items === 1;
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
