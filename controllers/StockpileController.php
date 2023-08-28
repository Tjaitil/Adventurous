<?php

namespace App\controllers;

use App\libs\Request;
use App\libs\Response;
use App\libs\controller;
use App\models\Inventory;
use App\models\Stockpile;
use App\services\SessionService;
use Respect\Validation\Validator;
use App\services\InventoryService;
use App\models\UserData;
use Illuminate\Support\Collection;

class StockpileController extends controller
{
    function __construct(
        private InventoryService $inventoryService,
        private Inventory $inventory,
        private SessionService $sessionService,
    ) {

        parent::__construct(true);
    }

    public function index()
    {
        $Stockpile = $this->get();

        $stockpile_max_amount = UserData::where('username', $this->sessionService->getCurrentUsername())
            ->first()
            ->stockpile_max_amount;

        $this->render('stockpile', 'Stockpile', [
            'Stockpile' => $Stockpile,
            'max_amount' => $stockpile_max_amount
        ], true, true);
    }

    /**
     * 
     * @return Collection<int, Stockpile>
     */
    public function get()
    {
        return Stockpile::where('username', $this->sessionService->getCurrentUsername())->get();
    }

    /**
     * 
     * @param Request $request 
     * @return Response
     */
    public function update(Request $request)
    {
        $item = $request->getInput('item');
        $amount = $request->getInput('amount');
        $insert = $request->getInput('insert');

        $request->validate([
            'item' => Validator::stringVal()->notEmpty(),
            'amount' => Validator::intVal(),
            'insert' => Validator::boolVal()
        ]);

        $stockpile_max_amount = UserData::where('username', $this->sessionService->getCurrentUsername())
            ->first()
            ->stockpile_max_amount;

        if ($amount === $stockpile_max_amount && $insert === true) {
            return Response::addMessage('You can\'t store more than $stockpile_max_amount items in your stockpile');
        }

        $StockpileItem = Stockpile::where('item', $item)
            ->where('username', $this->sessionService->getCurrentUsername())
            ->first();

        if (!$StockpileItem instanceof Stockpile) {
            $matched_stockpile_item = false;
        } else {
            $matched_stockpile_item = true;
        }

        if ($insert === true || $insert == 1) {
            $InventoryItem = $this->inventoryService->findItem($item);
            // Check if the user has the inventory item and correct amount
            if (!$InventoryItem instanceof Inventory) {
                return Response::addMessage('You don\'t have the item in your inventory')->setStatus(400);
            } else if ($InventoryItem->amount < $amount) {
                return Response::addMessage('You don\'t have that many in your inventory')->setStatus(400);
            }


            // If stockpile item does not exists, create ite
            if ($matched_stockpile_item === false) {
                Stockpile::create(
                    [
                        'username' => $this->sessionService->getCurrentUsername(),
                        'item' => $item,
                        'amount' => $amount
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
                return Response::addMessage('You don\'t have that item in your stockpile')->setStatus(400);
            } else if ($StockpileItem->amount < $amount) {
                return Response::addMessage('You don\'t have that many in your stockpile')->setStatus(400);
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
        $Stockpile = $this->get();
        $blade = $this->getTemplate([$Stockpile, $stockpile_max_amount]);
        return Response::addTemplate("stockpile", $blade);
    }

    /**
     *
     * @param array $bladeData
     * @return void
     */
    public function getTemplate(array $bladeData)
    {
        return $this->bladeRender->run('components.stockpile.itemList', ['Stockpile' => $bladeData[0], 'max_amount' => $bladeData[1]]);
    }
}
