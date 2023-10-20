<?php

namespace App\Services;

use App\libs\Response;
use App\Models\Inventory;
use \Exception;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property Collection $inventory_items
 */
class InventoryService
{
    private $inventory_items = [];

    public function __construct(
        protected Inventory $inventory,
        protected SessionService $sessionService
    ) {
        $this->getInventory();
    }



    /**
     * Get inventory
     *
     * @return Collection
     */
    public function getInventory()
    {

        return $this->inventory_items = $this->inventory->all()->where('username', $this->sessionService->user());
    }



    /**
     * Find item in inventory
     *
     * @param string $item
     *
     * @throws Exception If user does not have item
     * @return null|Inventory
     */
    public function findItem(string $item)
    {
        $this->getInventory();

        $item = $this->inventory_items->first(function ($value, $key) use ($item) {
            return $value->item === $item;
        });

        return $item;
    }



    /**
     * Check if user has enough of a item
     *
     * @param string $name
     * @param int $amount
     *
     * @return bool
     */
    public function hasEnoughAmount(string $name, int $amount)
    {
        $item_data = $this->findItem($name);

        // if ($this->checkSkipInventory()) {
        //     return true;
        // }
        if ($item_data === null || $item_data->amount < $amount) {
            return false;
        } else {
            return true;
        }
    }



    /**
     * Log when user does not have enough amount
     *
     * @param string $name Item name
     *
     * @return Response
     */
    public function logNotEnoughAmount(string $name)
    {
        return Response::addMessage(sprintf("You don't have enough of %s", $name))->setStatus(400);
    }



    /**
     * Edit item in inventory
     *
     * @param string $item Item to edit
     * @param int $amount Positive will add and negative will subtract
     * 
     * @throws Exception On full inventory
     * @return self
     */
    public function edit(string $item, int $amount)
    {
        if (count($this->inventory_items) === 0) {
            $this->getInventory();
        }
        $InventoryItem = $this->findItem($item);

        $new_amount = (is_null($InventoryItem)) ? $amount : $InventoryItem->amount + $amount;

        if ($this->inventory_items->count() >= 18 && !$InventoryItem && $new_amount > 0) {

            throw new Exception("Inventory is full!");
        } else if ($InventoryItem === null) {

            Inventory::create([
                'item' => $item,
                'amount' => $amount,
                'username' => $this->sessionService->getCurrentUsername()
            ]);
        } else {

            if ($new_amount <= 0) {

                $InventoryItem->delete();
            } else {

                $InventoryItem->amount = $new_amount;
                $InventoryItem->save();
            }
        }
        return $this;
    }



    public function checkSkipInventory()
    {
        if (boolval($_ENV['skip_inventory']) === true) {
            return true;
        }
        return false;
    }
}
