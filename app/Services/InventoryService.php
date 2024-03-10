<?php

namespace App\Services;

use App\Exceptions\InventoryFullException;
use App\Http\Responses\AdvResponse;
use App\Models\Inventory;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @property Collection $inventory_items
 */
class InventoryService
{
    /**
     * @var Collection<Inventory>
     */
    private ?Collection $inventory_items;

    public function __construct(
        protected Inventory $inventory,
    ) {
        $this->inventory_items = $this->getInventory();
    }

    /**
     * Get inventory
     *
     * @return Collection
     */
    public function getInventory()
    {
        return $this->inventory_items = $this->inventory->all()->where('username', Auth::user()->username);
    }

    /**
     * Find item in inventory
     *
     *
     * @return null|Inventory
     *
     * @throws Exception If user does not have item
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
     * @param  null|int  $plusAmont  Can be used to check if inventory is full included a plus amount
     */
    public function isInventoryIsFull(?int $plusAmont = null): bool
    {
        $currentCount = Inventory::where('username', Auth::user()->username)->count();
        if (isset($plusAmont)) {
            return $currentCount += $plusAmont > 18;
        } else {
            return $currentCount >= 18;
        }
    }

    public function handleInventoryFull(): JsonResponse
    {
        return (new AdvResponse([], 422))
            ->addErrorMessage('Inventory is full')->toResponse(request());
    }

    /**
     * Log when user does not have enough amount
     *
     * @param  string  $name  Item name
     */
    public function logNotEnoughAmount(string $name): JsonResponse
    {
        return (new AdvResponse([], 422))
            ->addErrorMessage(sprintf("You don't have enough of %s", $name))->toResponse(request());
    }

    /**
     * Edit item in inventory
     *
     * @param  string  $item  Item to edit
     * @param  int  $amount  Positive will add and negative will subtract
     * @return self
     *
     * @throws Exception On full inventory
     */
    public function edit(string $item, int $amount)
    {
        $InventoryItem = $this->findItem($item);

        $new_amount = (is_null($InventoryItem)) ? $amount : $InventoryItem->amount + $amount;

        if ($this->inventory_items->count() >= 18 && ! $InventoryItem && $new_amount > 0) {

            throw new InventoryFullException();
        } elseif ($InventoryItem === null) {

            Inventory::create([
                'item' => $item,
                'amount' => $amount,
                'username' => Auth::user()->username,
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

    public function checkSkipInventory(): bool
    {
        if (boolval($_ENV['skip_inventory']) === true) {
            return true;
        }

        return false;
    }
}
