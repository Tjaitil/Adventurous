<?php

namespace App\Http\Controllers;

use App\Http\Responses\AdvResponse;
use App\Models\Inventory;
use App\Models\Stockpile;
use App\Models\UserData;
use App\Services\InventoryService;
use App\Services\SessionService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;

class StockpileController extends Controller
{
    public function __construct(
        private InventoryService $inventoryService,
        private Inventory $inventory,
        private SessionService $sessionService,
    ) {
    }

    public function index()
    {
        $stockpile = $this->get();

        $stockpile_max_amount = UserData::where('username', $this->sessionService->getCurrentUsername())
            ->first()
            ->stockpile_max_amount;

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

    /**
     * @return AdvResponse
     */
    public function update(Request $request)
    {
        $item = $request->string('item');
        $amount = $request->input('amount');
        $insert = $request->boolean('insert');

        $Response = new AdvResponse();

        $request->validate([
            'item' => 'string|min:1',
            'amount' => 'numeric|min:1',
            'insert' => 'boolean',
        ]);

        $stockpile_max_amount = UserData::where('username', $this->sessionService->getCurrentUsername())
            ->first()
            ->stockpile_max_amount;

        if ($amount === $stockpile_max_amount && $insert === true) {
            return $Response->addMessage('You can\'t store more than $stockpile_max_amount items in your stockpile')
                ->setStatus(422)->toResponse($request);
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
            $InventoryItem = $this->inventoryService->findItem($item);
            // Check if the user has the inventory item and correct amount
            if (! $InventoryItem instanceof Inventory) {
                return $Response->addMessage('You don\'t have the item in your inventory')
                    ->setStatus(400)->toResponse($request);
            } elseif ($InventoryItem->amount < $amount) {
                return $Response->addMessage('You don\'t have that many in your inventory')
                    ->setStatus(400)->toResponse($request);
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
            if ($matched_stockpile_item === false) {
                return $Response->addMessage('You don\'t have that item in your stockpile')
                    ->setStatus(400)
                    ->toResponse($request);
            } elseif ($StockpileItem->amount < $amount) {
                return $Response->addMessage('You don\'t have that many in your stockpile')
                    ->setStatus(400)
                    ->toResponse($request);
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
        $this->inventoryService->edit($item, $adjust_inventory_item_amount);
        $blade = view('components.stockpile.itemList')
            ->with('stockpile', $this->get())
            ->with('max_amount', $stockpile_max_amount)
            ->render();

        return $Response->addTemplate('stockpile', $blade)->toResponse($request);
    }

    /**
     * @return string
     */
    public function getTemplate(array $bladeData)
    {
        return Blade::render('components.stockpile.itemList', ['stockpile' => $bladeData[0], 'max_amount' => $bladeData[1]]);
    }
}
