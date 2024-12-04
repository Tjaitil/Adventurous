<?php

namespace App\Http\Controllers;

use App\Http\Responses\AdvResponse;
use App\Models\Inventory;
use App\Models\Stockpile;
use App\Models\User;
use App\Models\UserData;
use App\Services\GameLogService;
use App\Services\InventoryService;
use App\Services\SessionService;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class StockpileController extends Controller
{
    public function __construct(
        private InventoryService $inventoryService,
        private SessionService $sessionService,
    ) {}

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        $stockpile = $this->get();

        $stockpile_max_amount = UserData::where('username', $this->sessionService->getCurrentUsername())
            ->first()?->stockpile_max_amount;

        return view('stockpile')
            ->with('title', 'Stockpile')
            ->with('stockpile', $stockpile)
            ->with('max_amount', $stockpile_max_amount);
    }

    /**
     * @return Collection<int, Stockpile>
     */
    public function get()
    {
        return Stockpile::where('username', $this->sessionService->getCurrentUsername())->get();
    }

    public function update(#[CurrentUser] User $User, Request $request): AdvResponse
    {
        $item = $request->string('item');
        $amount = $request->input('amount');
        $insert = $request->boolean('insert');

        $request->validate([
            'item' => 'string|min:1',
            'amount' => 'numeric|min:1',
            'insert' => 'boolean',
        ]);

        $stockpile_max_amount = UserData::where('username', $this->sessionService->getCurrentUsername())
            ->first('stockpile_max_amount')?->stockpile_max_amount;

        if ($amount === $stockpile_max_amount && $insert === true) {
            return advResponse([], 422)
                ->addMessage(GameLogService::addErrorLog('You can\'t store more than $stockpile_max_amount items in your stockpile'));
        }

        $StockpileItem = Stockpile::where('item', $item)
            ->where('username', $this->sessionService->getCurrentUsername())
            ->first();

        if (! $StockpileItem instanceof Stockpile) {
            $matched_stockpile_item = false;
        } else {
            $matched_stockpile_item = true;
        }

        if ($insert === true) {
            $InventoryItem = $this->inventoryService->findItem($User->inventory, $item);
            // Check if the user has the inventory item and correct amount
            if (! $InventoryItem instanceof Inventory) {
                return advResponse([], 400)
                    ->addMessage(GameLogService::addErrorLog('You don\'t have the item in your inventory'));
            } elseif ($InventoryItem->amount < $amount) {
                return advResponse([], 400)
                    ->addMessage(GameLogService::addErrorLog('You don\'t have that many in your inventory'));
            }

            // If stockpile item does not exists, create ite
            if ($matched_stockpile_item === false) {
                Stockpile::create(
                    [
                        'username' => $this->sessionService->getCurrentUsername(),
                        'item' => $item,
                        'amount' => $amount,
                    ]
                );
            } else {
                $new_stockpile_amount = $StockpileItem->amount + $amount;
                $StockpileItem->amount = $new_stockpile_amount;
                $StockpileItem->save();
            }

            $adjust_inventory_item_amount = -$amount;
        } else {
            if ($this->inventoryService->isInventoryIsFull($User->inventory->count())) {
                return $this->inventoryService->handleInventoryFull();
            }

            if ($matched_stockpile_item === false) {

                return advResponse([], 400)
                    ->addMessage(GameLogService::addErrorLog('You don\'t have that item in your stockpile'));
            } elseif ($StockpileItem->amount < $amount) {
                return advResponse([], 400)
                    ->addMessage(GameLogService::addErrorLog('You don\'t have that many in your stockpile'));
            }

            // If stockpile item does not exists, create ite
            if ($StockpileItem->amount - $amount === 0) {
                $StockpileItem->delete();
            } else {
                $StockpileItem->amount = $StockpileItem->amount - $amount;
                $StockpileItem->save();
            }

            $adjust_inventory_item_amount = $amount;
        }
        // Update inventory
        $this->inventoryService->edit($User->inventory, $item, $adjust_inventory_item_amount, $User->id);
        $blade = view('components.stockpile.itemList')
            ->with('stockpile', $this->get())
            ->with('max_amount', $stockpile_max_amount)
            ->render();

        return advResponse()->addTemplate('stockpile', $blade);
    }
}
