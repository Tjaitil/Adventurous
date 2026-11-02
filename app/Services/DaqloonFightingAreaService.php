<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class DaqloonFightingAreaService
{
    /**
     * Remember whether the map the user is on has a daqloon fighting area.
     * Stored in the cache, which is Redis when CACHE_DRIVER=redis.
     */
    public function track(int $userId, bool $hasFightingArea): void
    {
        Cache::forever($this->key($userId), $hasFightingArea);
    }

    public function isInFightingArea(int $userId): bool
    {
        return Cache::get($this->key($userId), false) === true;
    }

    private function key(int $userId): string
    {
        return 'daqloon_fighting_area:'.$userId;
    }
}
