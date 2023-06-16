<?php

namespace App\controllers;

use App\actions\MapRequiredDataAction;
use App\libs\controller;
use App\libs\Request;
use App\libs\Response;
use App\models\Bakery_model;
use App\services\InventoryService;
use App\services\StoreService;
use GameConstants;
use Exception;

class BakeryController extends controller
{
    public $data = [];

    protected InventoryService $inventoryService;
    protected Bakery_model $bakery_model;
    protected StoreService $storeService;
    protected MapRequiredDataAction $mapRequiredDataAction;

    function __construct(
        InventoryService $inventoryService,
        Bakery_model $bakery_model,
        StoreService $storeService,
        MapRequiredDataAction $mapRequiredDataAction,
    ) {
        parent::__construct();
        $this->inventoryService = $inventoryService;
        $this->bakery_model = $bakery_model;
        $this->storeService = $storeService;
        $this->mapRequiredDataAction = $mapRequiredDataAction;
    }


    public function index()
    {
        $this->loadModel('Bakery', true);
        $this->data = $this->model->getData();
        $this->render('bakery', 'Bakery', $this->data, true, true);
    }

    public function get()
    {
        return Response::setData($this->mapRequiredDataAction->handle($this->bakery_model->all()));
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
        $amount = $request->getInput('amount');

        try {
            $this->storeService->makeStore(
                ["list" => $this->mapRequiredDataAction->handle($this->bakery_model->find($item))]
            );
            if ($this->storeService->isStoreItem($item)) {
                return $this->storeService->logNotStoreItem($item);
            }

            $item = $this->storeService->getStoreItem($item);

            foreach ($item->required_items as $key => $value) {
                if ($this->inventoryService->hasEnoughAmount($value->name, $value->required_amount * $amount)) {
                    return $this->inventoryService->logNotEnoughAmount($value->name);
                    break;
                }
            }

            foreach ($item as $key => $value) {
                $this->inventoryService->edit($value->name, $amount);
            }
            $this->inventoryService->edit(
                GameConstants::CURRENCY,
                $this->storeService->calculateItemCost($item, $amount)
            );

            return Response::setStatus(200);
        } catch (Exception $e) {
            return Response::addMessage($e->getMessage());
        }
    }
}
