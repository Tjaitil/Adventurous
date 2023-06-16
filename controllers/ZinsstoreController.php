<?php

namespace App\controllers;

use App\libs\controller;
use App\libs\Request;
use App\libs\Response;
use App\models\Item;
use App\services\InventoryService;
use App\services\StoreService;
use \Exception;

class ZinsstoreController extends controller
{
    public $data;


    function __construct(
        private StoreService $storeService,
        private InventoryService $inventoryService,
    ) {
        parent::__construct();
    }

    public function index()
    {
        $this->render('zinsstore', 'Zins Store', $this->data, true, true);
    }

    public function buy(Request $request)
    {
        $item = $request->getInput('item');
        $amount = $request->getInput('amount');

        try {
            $store_items = Item::whereIn('name', ['daqloon horns', 'daqloon scale']);
            $this->storeService->makeStore(["list" => $store_items]);

            $this->inventoryService->hasEnoughAmount($item, $amount);
            $matched_item = $this->storeService->isStoreItem($item);

            // Update data
            $this->inventoryService->edit('gold', $matched_item[0]->value * $amount);
            $this->inventoryService->edit($item, -$amount);
        } catch (Exception $e) {
            Response::addMessage($e->getMessage());
        }
    }
}
