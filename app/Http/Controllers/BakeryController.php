<?php

namespace App\controllers;

use App\libs\controller;
use App\libs\Request;
use App\libs\Response;
use App\services\InventoryService;
use App\services\StoreService;
use App\Stores\BakeryStore;
use App\validators\ValidateStoreTrade;

class BakeryController extends controller
{

    /**
     * 
     * @param InventoryService $inventoryService 
     * @param StoreService $storeService 
     * @param SessionService $sessionService 
     * @return void 
     */
    function __construct(
        protected InventoryService $inventoryService,
        protected StoreService $storeService,
        protected BakeryStore $bakeryStore
    ) {
        parent::__construct();
    }

    /**
     * 
     * @return void 
     */
    public function index()
    {
        $storeResource = $this->bakeryStore->getStore();
        $this->render('bakery', 'Bakery', ['store_resource' => $storeResource], true, true, true);
    }

    /**
     * 
     * @return Response 
     */
    public function getStoreItems()
    {
        return $this->bakeryStore->getStoreItemsResponse();
    }

    /**
     *
     * @param Request $request
     * @return Response
     */
    public function makeItem(Request $request)
    {
        $item = $request->getInput('item');
        $amount = intval($request->getInput('amount'));

        ValidateStoreTrade::validate($request);

        $initial_store = $this->bakeryStore->makeStore();
        $this->storeService->storeBuilder->setResource($initial_store);

        if (!$this->storeService->isStoreItem($item)) {
            return $this->storeService->logNotStoreItem($item);
        }
        $store_item = $this->storeService->getStoreItem($item);

        if (!$this->storeService->hasRequiredItems($item, $amount)) {
            return $this->storeService->logNotEnoughAmount();
        }

        foreach ($store_item->required_items as $key => $value) {
            $this->inventoryService->edit($value->name, -$amount);
        }

        if (!$this->inventoryService->hasEnoughAmount(CURRENCY, $store_item->store_value * $amount)) {
            return $this->inventoryService->logNotEnoughAmount(CURRENCY);
        } else {
            $this->inventoryService->edit(CURRENCY, -$store_item->store_value * $amount);
        }

        $this->inventoryService->edit($item, $amount);

        return Response::setStatus(200);
    }
}
