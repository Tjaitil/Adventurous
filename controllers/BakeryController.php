<?php

namespace App\controllers;

use App\libs\controller;
use App\libs\Request;
use App\libs\Response;
use App\models\HealingItem;
use App\resources\StoreResource;
use App\services\InventoryService;
use App\services\SessionService;
use App\services\StoreDiscountService;
use App\services\StoreService;
use App\validators\ValidateStoreTrade;

class BakeryController extends controller
{

    /**
     * 
     * @param InventoryService $inventoryService 
     * @param StoreService $storeService 
     * @param SessionService $sessionService 
     * @param StoreDiscountService $storeDiscountService 
     * @return void 
     */
    function __construct(
        protected InventoryService $inventoryService,
        protected StoreService $storeService,
        protected SessionService $sessionService,
        protected StoreDiscountService $storeDiscountService,
    ) {
        parent::__construct();
    }

    /**
     * 
     * @return void 
     */
    public function index()
    {
        $this->render('bakery', 'Bakery', ['store_resource' => $this->makeShop()], true, true, true);
    }

    /**
     * 
     * @return Response 
     */
    public function getStoreItems()
    {
        return Response::addData("store_items", $this->makeShop()->toArray()['store_items'])->setStatus(200);
    }

    /**
     * Make item
     *
     * @param Request $request
     *
     * @return Response
     */
    public function makeItem(Request $request)
    {

        $item = $request->getInput('item');
        $amount = intval($request->getInput('amount'));

        ValidateStoreTrade::validate($request);

        $store_item = HealingItem::with('requiredItems')->where('bakery_item', 1)->get();

        $this->storeService->makeStore(
            ["store_items" => $store_item]
        )
            ->storeBuilder
            ->setAdjustedStoreValue($this->storeDiscountService->getDiscount('bakery'));

        if (!$this->storeService->isStoreItem($item)) {
            return $this->storeService->logNotStoreItem($item);
        }

        $store_item = $this->storeService->getStoreItem($item);
        foreach ($store_item->required_items as $key => $value) {
            if (!$this->inventoryService->hasEnoughAmount($value->name, $value->amount * $amount)) {
                return $this->inventoryService->logNotEnoughAmount($value->name);
                break;
            }
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

    /**
     * 
     * @return StoreResource
     */
    protected function makeShop()
    {
        $BakeryItems = HealingItem::with('requiredItems')->where('bakery_item', 1)->get();


        return $this->storeService->makeStore(["store_items" => $BakeryItems])
            ->storeBuilder
            ->setAdjustedStoreValue($this->storeDiscountService->getDiscount('bakery'))
            ->setStoreName('bakery')
            ->setInfiniteAmount(true)
            ->build();
    }
}
