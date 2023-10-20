<?php

namespace App\Http\Controllers;

use App\libs\controller;
use App\libs\Request;
use App\libs\Response;
use App\Models\Trader;
use App\Models\TravelBureauCart;
use App\Services\SessionService;
use App\Services\InventoryService;
use App\Services\StoreService;
use App\Stores\TravelBureauStore;

class TravelBureauController extends controller
{

    /**
     * 
     * @param StoreService $storeService 
     * @param InventoryService $inventoryService 
     * @param SessionService $sessionService 
     * @return void 
     */
    function __construct(
        private StoreService $storeService,
        private InventoryService $inventoryService,
        private SessionService $sessionService,
        private TravelBureauStore $travelBureauStore
    ) {
        parent::__construct();
    }

    public function index()
    {
        $current_cart = Trader::where('username', $this->sessionService->getCurrentUsername())->first()->cart;
        $store_resource = $this->travelBureauStore->getStore();

        $this->render('travelbureau', 'Travel Bureau', [
            'store_resource' => $store_resource,
            'current_cart' => $current_cart
        ], true, true, true);
    }

    /**
     * @return Response
     */
    public function getStoreItems()
    {
        return $this->travelBureauStore->getStoreItemsResponse();
    }

    /**
     * 
     * @param Request $request 
     * @return Response
     * @throws \Exception 
     */
    public function buyCart(Request $request)
    {
        $item = $request->getInput('item');
        $amount = 1;
        $initial_store = $this->travelBureauStore->makeStore([$item]);
        $this->storeService->storeBuilder->setResource($initial_store);

        $Trader = Trader::where('username', $this->sessionService->getCurrentUsername())->first();
        $Cart = TravelBureauCart::where('name', $item)->first();
        if (!$this->storeService->isStoreItem($item)) {
            return $this->storeService->logNotStoreItem($item);
        }
        $store_item = $this->storeService->getStoreItem($item);

        if ($Cart->id === $Trader->cart_id) {
            return Response::setStatus(422)->addMessage("You already have this cart");
        }

        $this->storeService->hasRequiredItems($item, $amount);

        foreach ($store_item->required_items as $key => $value) {
            if (!$this->inventoryService->hasEnoughAmount(
                $value->name,
                $value->amount * $amount
            )) {
                return $this->inventoryService->logNotEnoughAmount($value->name);
            }
        }

        if (!$this->inventoryService->hasEnoughAmount(CURRENCY, $store_item->store_value)) {
            return $this->inventoryService->logNotEnoughAmount(CURRENCY);
        }

        foreach ($store_item->required_items as $key => $value) {
            $this->inventoryService->edit($value->name, $value->amount * $amount);
        }

        if (!$this->inventoryService->hasEnoughAmount(CURRENCY, $store_item->store_value)) {
            return $this->inventoryService->logNotEnoughAmount(CURRENCY);
        } else {

            $this->inventoryService
                ->edit(
                    CURRENCY,
                    -$this->storeService->calculateItemCost($store_item->name, $amount)
                );
        }

        $Trader->cart_id = $Cart->id;
        $Trader->save();

        return Response::setStatus(200)
            ->addMessage(sprintf("You bought %s", $item))
            ->addData("new_cart", $item);
    }
}
