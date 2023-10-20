<?php

namespace App\tests\support\DatabaseUtils;

use App\Models\Inventory;
use App\tests\support\SessionTrait;

trait InventoryTrait
{
    use SessionTrait;

    /**
     * 
     * @param array $items 
     * @property string $items.name
     * @property int $items.amount
     * @return void 
     */
    public function insertInventoryItems(array $items, ?string $username = null)
    {
        foreach ($items as $key => $item) {
            Inventory::insert([
                'username' => $username ?? self::$username,
                'item' => $item['name'],
                'amount' => $item['amount'],
            ]);
        }
    }

    /**
     * 
     * @param string $item 
     * @param int $amount 
     * @param null|string $username 
     * @return void 
     */
    public function insertInventoryItem(string $item, int $amount, ?string $username = null)
    {
        $this->insertInventoryItems([['name' => $item, 'amount' => $amount]], $username);
    }
}
