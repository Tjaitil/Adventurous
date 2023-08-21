<?php

namespace App\controllers;

use App\libs\controller;
use App\libs\Request;
use App\libs\Response;
use App\models\Trader;
use App\models\TravelBureauCart;
use App\resources\StoreResource;
use App\services\SessionService;
use App\services\InventoryService;
use App\services\StoreService;

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
    ) {
        parent::__construct(true);
    }

    public function index()
    {
        $current_cart = Trader::where('username', $this->sessionService->getCurrentUsername())->first()->cart;
        $store_resource = $this->makeShop();

        $this->render('travelbureau', 'Travel Bureau', [
            'store_resource' => $store_resource,
            'current_cart' => $current_cart
        ], true, true);
    }

    /**
     * 
     * @return storeResource
     */
    protected function makeShop()
    {
        $Carts = TravelBureauCart::with('requiredItems', 'skillRequirements')->get();
        $this->storeService->makeStore(["list" => $Carts]);

        return $this->storeService
            ->storeBuilder->setInfiniteAmount(true)
            ->build();
    }

    /**
     * @return Response
     */
    public function getStoreItems()
    {
        return Response::addData("store_items", $this->makeShop()->toArray()['store_items'])->setStatus(200);
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
        $Cart = TravelBureauCart::where('name', $item)->with('requiredItems')->first();
        $Trader = Trader::where('username', $this->sessionService->getCurrentUsername())->first();
        if (!$Cart) {
            return Response::setStatus(422)->addMessage("This cart does not exist");
        } else if ($Cart->id === $Trader->cart_id) {
            return Response::setStatus(422)->addMessage("You already have this cart");
        }

        $this->storeService->makeStore(
            ["list" => [$Cart]]
        );


        if (!$this->storeService->isStoreItem($item)) {
            return $this->storeService->logNotStoreItem($item);
        }

        $item_data = $this->storeService->getStoreItem($item);

        foreach ($item_data->required_items as $key => $value) {
            if (!$this->inventoryService->hasEnoughAmount(
                $value->name,
                $value->amount * $amount
            )) {
                return $this->inventoryService->logNotEnoughAmount($value->name);
            }
        }

        if (!$this->inventoryService->hasEnoughAmount(CURRENCY, $item_data->store_value)) {
            return $this->inventoryService->logNotEnoughAmount(CURRENCY);
        }

        foreach ($item_data->required_items as $key => $value) {
            $this->inventoryService->edit($value->name, $value->amount * $amount);
        }

        if (!$this->inventoryService->hasEnoughAmount(CURRENCY, $item_data->store_value)) {
            return $this->inventoryService->logNotEnoughAmount(CURRENCY);
        } else {

            $this->inventoryService
                ->edit(
                    CURRENCY,
                    -$this->storeService->calculateItemCost($item_data->name, $amount)
                );
        }

        $Trader->cart_id = $Cart->id;
        $Trader->save();

        return Response::setStatus(200)
            ->addMessage(sprintf("You bought %s", $item))
            ->addData("new_cart", $item);
    }
}
