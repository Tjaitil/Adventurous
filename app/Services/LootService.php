<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Support\Facades\Cache;

class LootService
{
    /**
     * Item names a daqloon can drop. Each must exist in the items table.
     *
     * @var array<int, string>
     */
    public const DROPS = ['daqloon horns', 'daqloon scale'];

    /**
     * Roll a drop and store it as pending for the user.
     *
     * @return string|null Item name, or null if nothing was rolled
     */
    public function roll(int $userId): ?string
    {
        $pending = $this->getPending($userId);

        $name = Item::whereIn('name', self::DROPS)->inRandomOrder()->value('name');
        if ($name === null) {
            return null;
        }

        $pending[] = $name;
        Cache::forever($this->key($userId), $pending);

        return $name;
    }

    /**
     * The oldest pending drop, without removing it.
     */
    public function peekPending(int $userId): ?string
    {
        return $this->getPending($userId)[0] ?? null;
    }

    public function consumePending(int $userId): void
    {
        $pending = $this->getPending($userId);
        array_shift($pending);
        Cache::forever($this->key($userId), $pending);
    }

    /**
     * @return array<int, string>
     */
    private function getPending(int $userId): array
    {
        return Cache::get($this->key($userId), []);
    }

    private function key(int $userId): string
    {
        return 'pending_loot:'.$userId;
    }
}
