<?php

namespace App\Http\Controllers;

use App\libs\controller;
use App\libs\Request;
use App\libs\Response;
use App\Services\InventoryService;
use App\Services\StoreService;
use App\Stores\ZinssStore;

class ZinsstoreController extends controller
{
    public $data;


    function __construct(
        private ZinssStore $zinssStore,
        private StoreService $storeService,
        private InventoryService $inventoryService,
    ) {
        parent::__construct();
    }

    public function index()
    {
        $storeResource = $this->zinssStore->getStore();
        $this->render('zinsstore', 'Zins Store', ['store_resource' => $storeResource], true, true, true);
    }

    /**
     * 
     * @return Response 
     */
    public function getStoreItems()
    {
        return $this->zinssStore->getStoreItemsResponse();
    }

    /**
     * 
     * @param Request $request 
     * @return Response 
     */
    public function buy(Request $request)
    {
        $item = $request->getInput('item');
        $amount = $request->getInput('amount');

        $initial_store = $this->zinssStore->makeStore([$item]);
        $this->storeService->storeBuilder->setResource($initial_store);

        if (!$this->inventoryService->hasEnoughAmount($item, $amount)) {
            return $this->inventoryService->logNotEnoughAmount($item);
        }

        if (!$this->storeService->isStoreItem($item)) {
            return $this->storeService->logNotStoreItem($item);
        }
        $store_item = $this->storeService->getStoreItem($item);

        if (!$this->inventoryService->hasEnoughAmount(CURRENCY, $store_item->store_value * $amount)) {
            return $this->inventoryService->logNotEnoughAmount(CURRENCY);
        } else {
            $this->inventoryService->edit(CURRENCY, $store_item->store_value * $amount);
        }

        $this->inventoryService->edit($item, -$amount);
        return Response::setStatus(200);
    }
}
