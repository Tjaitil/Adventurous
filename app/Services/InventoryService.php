<?php

namespace App\Services;

use App\Events\InventoryUpdated;
use App\Exceptions\InventoryFullException;
use App\Http\Responses\AdvResponse;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Log;

class InventoryService
{
    public const DEFAULT_MAX_SLOTS = 18;

    public function __construct(
    ) {}

    /**
     * Get the number of inventory slots available to a user, falling back
     * to the default when they don't have a custom limit set.
     */
    public function getMaxSlots(User $user): int
    {
        return $user->userData->inventory_max_slots ?? self::DEFAULT_MAX_SLOTS;
    }

    /**
     * Find item in inventory
     *
     * @param  Collection<int, Inventory>  $Inventory
     * @param  string  $item  Item name
     * @return null|Inventory
     *
     * @throws Exception If user does not have item
     */
    public function findItem(Collection $Inventory, string $item)
    {
        $itemId = Item::where('name', $item)->value('item_id');

        if ($itemId === null) {
            return null;
        }

        return $Inventory->firstWhere('item_id', $itemId);
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
    public function isInventoryIsFull(User $user, ?int $plusAmont = null): bool
    {
        $inventoryCount = $user->inventory->count();
        $maxSlots = $this->getMaxSlots($user);

        if (isset($plusAmont)) {
            return $inventoryCount + $plusAmont > $maxSlots;
        } else {
            return $inventoryCount >= $maxSlots;
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

        $new_amount = (is_null($InventoryItem)) ? $amount : $InventoryItem->amount + $amount;

        $user = User::findOrFail($userId);
        $maxSlots = $this->getMaxSlots($user);

        if ($Inventory->count() >= $maxSlots && ! $InventoryItem && $new_amount > 0) {

            throw new InventoryFullException;
        } elseif ($InventoryItem === null) {
            $itemId = Item::where('name', $item)->value('item_id');

            Inventory::create([
                'item_id' => $itemId,
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
        event(new InventoryUpdated($Inventory, $user));

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
