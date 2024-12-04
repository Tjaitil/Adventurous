<?php

namespace Tests\Support;

use App\Models\Inventory;
use App\Models\User;

trait InventoryTrait
{
    public function insertItemToInventory(User $User, string $name, int $amount): void
    {
        Inventory::upsert(
            [
                'user_id' => $User->id,
                'item' => $name,
                'amount' => $amount,
            ], ['user_id', 'item'],
        );
    }

    public function insertCurrencyToInventory(User $User, int $amount): void
    {
        Inventory::upsert(
            [
                'user_id' => $User->id,
                'item' => config('adventurous.currency'),
                'amount' => $amount,
            ], ['user_id', 'item'],
        );
    }
}
