<?php

namespace Tests\Support;

use App\Models\Inventory;
use App\Models\Item;
use App\Models\User;

trait InventoryTrait
{
    public function insertItemToInventory(User $User, string $name, int $amount): void
    {
        $itemId = Item::where('name', $name)->value('item_id');

        Inventory::upsert(
            [
                'user_id' => $User->id,
                'item_id' => $itemId,
                'amount' => $amount,
            ], ['user_id', 'item_id'],
        );
    }

    public function insertCurrencyToInventory(User $User, int $amount): void
    {
        $itemId = Item::where('name', config('adventurous.currency'))->value('item_id');

        Inventory::upsert(
            [
                'user_id' => $User->id,
                'item_id' => $itemId,
                'amount' => $amount,
            ], ['user_id', 'item_id'],
        );
    }
}
