<?php

namespace App\Services;

use App\Exceptions\InventoryFullException;
use App\Http\Responses\AdvResponse;
use App\Models\Inventory;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Log;

class InventoryService
{
    public function __construct(
    ) {}

    /**
     * Find item in inventory
     *
     * @param  Collection<int, Inventory>  $Inventory
     * @return null|Inventory
     *
     * @throws Exception If user does not have item
     */
    public function findItem(Collection $Inventory, string $item)
    {
        $item = $Inventory->firstWhere('item', $item);

        return $item;
    }

    /**
     * Check if user has enough of a item
     *
     * @param  Collection<int, Inventory>  $Inventory
     * @return bool
     */
    public function hasEnoughAmount(Collection $Inventory, string $name, int $amount)
    {
        $item_data = $this->findItem($Inventory, $name);

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
    public function isInventoryIsFull(int $inventoryCount, ?int $plusAmont = null): bool
    {
        if (isset($plusAmont)) {
            $newAmount = $inventoryCount += $plusAmont;

            return $newAmount > 18;
        } else {
            return $inventoryCount >= 18;
        }
    }

    public function handleInventoryFull(): AdvResponse
    {
        $log = App::make(GameLogService::class)->addWarningLog('Inventory is full');

        return (new AdvResponse([], 422))
            ->addMessage($log);
    }

    /**
     * Log when user does not have enough amount
     *
     * @param  string  $name  Item name
     */
    public function logNotEnoughAmount(string $name): JsonResponse
    {
        return (new AdvResponse([], 422))
            ->addMessage(GameLogService::addErrorLog(sprintf("You don't have enough of %s", $name)))
            ->toResponse(request());
    }

    /**
     * Edit item in inventory
     *
     * @param  Collection<int, Inventory>  $Inventory
     * @param  string  $item  Item to edit
     * @param  int  $amount  Positive will add and negative will subtract
     * @return self
     *
     * @throws InventoryFullException
     */
    public function edit(Collection $Inventory, string $item, int $amount, int $userId)
    {
        $InventoryItem = $this->findItem($Inventory, $item);
        Log::debug('InventoryItem', [$InventoryItem]);

        $new_amount = (is_null($InventoryItem)) ? $amount : $InventoryItem->amount + $amount;

        if ($Inventory->count() >= 18 && ! $InventoryItem && $new_amount > 0) {

            throw new InventoryFullException;
        } elseif ($InventoryItem === null) {
            // dd($amount);
            Inventory::create([
                'item' => $item,
                'amount' => $amount,
                'user_id' => $userId,
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
