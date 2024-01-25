<?php

namespace Tests\Support;

use App\Models\Inventory;

trait InventoryTrait {

    public function insertItemToInventory(string $username, string $name, int $amount): void {
        Inventory::insert(
            [
                'username' => $username,
                'item' => $name,
                'amount' => $amount,
            ],
        );
    }

    public function insertCurrencyToInventory(string $username, int $amount): void {
        Inventory::insert(
            [
                'username' => $username,
                'item' => config('adventurous.currency'),
                'amount' => $amount,
            ],
        );
    }
}