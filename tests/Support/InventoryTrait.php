<?php

namespace Tests\Support;

use App\Models\Inventory;
use App\Models\User;

trait InventoryTrait
{
    public function insertItemToInventory(User $User, string $name, int $amount): void
    {
        Inventory::insert(
            [
                'username' => $User->username,
                'item' => $name,
                'amount' => $amount,
            ],
        );
    }

    public function insertCurrencyToInventory(User $User, int $amount): void
    {
        Inventory::insert(
            [
                'username' => $User->username,
                'item' => config('adventurous.currency'),
                'amount' => $amount,
            ],
        );
    }
}
