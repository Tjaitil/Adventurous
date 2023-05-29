<?php

namespace App\controllers;

use App\libs\controller;
use App\libs\Request;
use App\libs\Response;
use App\models\Inventory;
use App\models\Stockpile;
use App\services\InventoryService;
use App\services\SessionService;
use App\services\TemplateFetcherService;
use \Exception;
use Respect\Validation\Validator;

class StockpileController extends controller
{
    public $data;

    function __construct(
        private InventoryService $inventoryService,
        private Stockpile $stockpile,
        private Inventory $inventory,
        private SessionService $sessionService,
    ) {

        parent::__construct();
    }

    public function index()
    {
        $this->data['stockpile'] =
            $this->stockpile->where('username', $this->sessionService->getCurrentUsername())
            ->get()
            ->toArray();
        $this->data['max_amount'] = MAX_STOCKPILE_AMOUNT;
        $this->render('stockpile', 'Stockpile', $this->data, true, true);
    }

    public function get()
    {
        return $this->stockpile->where('username', $this->sessionService->getCurrentUsername())->get()->toArray();
    }

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

        $item_data = $this->stockpile
            ->where('item', $item)
            ->where('username', $this->sessionService->getCurrentUsername())
            ->first();

        $matched_stockpile_item = true;

        if (!isset($item_data->amount)) {
            $matched_stockpile_item = false;
        }

        if ($insert === true || $insert == 1) {
            $matched_inventory_item = $this->inventoryService->findItem($item);
            // Check if the user has the inventory item and correct amount
            if ($matched_inventory_item === false) {
                throw new Exception("You don't have the item in your inventory");
            } else if ($matched_inventory_item['amount'] < $amount) {
                throw new Exception("You don't have that many in your inventory");
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
                $new_stockpile_amount = $item_data->amount + $amount;
                $item_data->amount = $new_stockpile_amount;
                $item_data->save();
            }

            $adjust_inventory_item_amount = -$amount;
        } else {

            if ($matched_stockpile_item === false) {
                throw new Exception("You don't have that item in your stockpile");
            } else if ($item_data->amount < $amount) {
                throw new Exception("You don't have that many in your stockpile");
            }

            $new_stockpile_amount = $item_data->amount - $amount;

            // If stockpile item does not exists, create ite
            if ($new_stockpile_amount === 0) {
                $item_data->delete();
            } else {
                $item_data->amount = $new_stockpile_amount;
                $item_data->save();
            }

            $adjust_inventory_item_amount = $amount;
        }

        // Update inventory
        $this->inventoryService->edit($item, $adjust_inventory_item_amount);
        $this->getTemplate();
    }

    public function getTemplate()
    {
        $data = array();

        $data['stockpile'] = $this->get();
        $template = TemplateFetcherService::loadTemplate('stockpile', $data);
        Response::addTemplate("stockpile", $template);
    }
}
