<?php

namespace App\Services;

use App\Http\Resources\StockpileCollection;
use App\Http\Responses\AdvResponse;
use App\Models\Item;
use App\Models\Stockpile;
use App\Models\User;
use App\Models\UserData;

class StockpileService
{
    public function __construct(
        private readonly InventoryService $inventoryService,
    ) {}

    /**
     * @param  array{item: string, amount: int, all: bool, insert: bool}  $input
     */
    public function update(User $user, array $input): AdvResponse|StockpileCollection
    {
        return $input['insert']
            ? $this->insert($user, $input['item'], $input['amount'])
            : $this->withdraw($user, $input['item'], $input['amount']);
    }

    public function insert(User $user, string $item, int $amount): AdvResponse|StockpileCollection
    {
        $itemId = Item::where('name', $item)->value('item_id');

        if ($itemId === null) {
            return (new AdvResponse([], 400))
                ->addMessage(GameLogService::addErrorLog("That item doesn't exist"));
        }

        $maxSlots = UserData::where('username', $user->username)->value('stockpile_max_amount');

        $stockpileItem = Stockpile::where('user_id', $user->id)
            ->where('item_id', $itemId)
            ->first();

        $canInsert = $stockpileItem !== null
            || Stockpile::where('user_id', $user->id)->count() < $maxSlots;

        if (! $canInsert) {
            return (new AdvResponse([], 422))
                ->addMessage(GameLogService::addErrorLog("You can't store more than {$maxSlots} items in your stockpile"));
        }

        $amountInInventory = $this->inventoryService->hasEnoughAmount(
            $user->inventory,
            $item,
            $amount,
        );
        if (! $amountInInventory) {
            return (new AdvResponse([], 400))
                ->addMessage(GameLogService::addErrorLog("You don't have enough of that item in your inventory"));
        }

        if (! $stockpileItem) {
            $stockpileItem = new Stockpile([
                'user_id' => $user->id,
                'item_id' => $itemId,
            ]);
        }

        $stockpileItem->amount = ($stockpileItem->amount ?? 0) + $amount;
        $stockpileItem->save();

        $this->inventoryService->edit($user->inventory, $item, -$amount, $user->id);

        return $this->getStockpileResource($user);
    }

    public function withdraw(User $user, string $item, int $amount): AdvResponse|StockpileCollection
    {
        if ($this->inventoryService->isInventoryIsFull($user, $user->inventory->count())) {
            return $this->inventoryService->handleInventoryFull();
        }

        $itemId = Item::where('name', $item)->value('item_id');

        $stockpileItem = $itemId === null ? null : Stockpile::where('user_id', $user->id)
            ->where('item_id', $itemId)
            ->first();

        if (! $stockpileItem) {
            return (new AdvResponse([], 400))
                ->addMessage(GameLogService::addErrorLog("You don't have that item in your stockpile"));
        }

        $amountToRemove = $amount;

        if ($stockpileItem->amount < $amountToRemove) {
            return (new AdvResponse([], 400))
                ->addMessage(GameLogService::addErrorLog("You don't have that many in your stockpile"));
        }

        if ($stockpileItem->amount === $amountToRemove) {
            $stockpileItem->delete();
        } else {
            $stockpileItem->amount -= $amountToRemove;
            $stockpileItem->save();
        }

        $this->inventoryService->edit($user->inventory, $item, $amountToRemove, $user->id);

        return $this->getStockpileResource($user);
    }

    public function getStockpileResource(User $user): StockpileCollection
    {
        return new StockpileCollection(
            Stockpile::where('user_id', $user->id)->with('item')->get()
        );
    }
}
