<?php

namespace App\controllers;

use App\actions\MapRequiredDataAction;
use App\libs\controller;
use App\libs\Logger;
use App\libs\Request;
use App\libs\Response;
use App\models\ArcheryShopData;
use App\services\InventoryService;
use App\services\SessionService;
use App\services\SkillsService;
use App\services\StoreService;
use App\validators\ValidateStoreTrade;
use GameConstants;


class ArcheryShopController extends controller
{
    public $data = array();

    function __construct(
        private StoreService $storeService,
        private InventoryService $inventoryService,
        private SessionService $sessionService,
        private ArcheryShopData $archeryShopData,
        private MapRequiredDataAction $mapRequiredDataAction
    ) {
        parent::__construct();
    }
    public function index()
    {
        $data['store_items'] = $this->makeArcherShopStore();
        $data['discount'] = $this->sessionService->isProfiency(GameConstants::MINER_SKILL_NAME) ? GameConstants::MINER_STORE_DISCOUNT * 100 : 0;

        $this->render('archeryshop', 'Archery Shop', $data, true, true);
    }

    public function makeArcherShopStore()
    {
        $data = $this->archeryShopData->all();
        $data = $this->mapRequiredDataAction->handle($data->toArray());
        $this->storeService->makeStore(
            ["list" => $data]
        );

        if ($this->sessionService->isProfiency(GameConstants::MINER_SKILL_NAME)) {
            $this->storeService->storeBuilder->setAdjustedStoreValue(GameConstants::MINER_STORE_DISCOUNT);
        }

        return $this->storeService->storeBuilder->build()->toArray();
    }

    /**
     * Get items
     *
     * @return Response
     */
    public function getItems()
    {
        $items = $this->makeArcherShopStore();

        return Response::addData("store_items", $items)->setStatus(200);
    }

    /**
     * Fletch item
     *
     * @param Request $request
     * @param MapRequiredDataAction $mapRequiredDataAction
     * @param SkillsService $skillsService
     *
     * @return Response
     */
    public function fletchItem(Request $request, MapRequiredDataAction $mapRequiredDataAction, SkillsService $skillsService)
    {
        $item = $request->getInput('item');
        $amount = $request->getInput('amount');

        ValidateStoreTrade::validate($request);

        $item_data = $this->archeryShopData->where('item', $item)->first();
        $item_data = $mapRequiredDataAction->handle([$item_data->toArray()]);

        $this->storeService->makeStore(
            ["list" => $item_data]
        );


        if (!$this->storeService->isStoreItem($item)) {
            return $this->storeService->logNotStoreItem($item);
        }

        $item = $this->storeService->getStoreItem($item);

        // Check that user has required level
        if (!$skillsService->hasRequiredLevel($item_data[0]['required_level'], MINER_SKILL_NAME)) {
            return $skillsService->logNotRequiredLevel(MINER_SKILL_NAME);
        }

        foreach ($item->required_items as $key => $value) {
            if (!$this->inventoryService->hasEnoughAmount(
                $value->name,
                $value->amount * $amount
            )) {
                return $this->inventoryService->logNotEnoughAmount($value->name);
            }
        }

        if (!$this->inventoryService->hasEnoughAmount(CURRENCY, $item->store_value)) {
            return $this->inventoryService->logNotEnoughAmount(CURRENCY);
        }

        $price = $item->store_value;

        if ($this->sessionService->isProfiency(MINER_SKILL_NAME)) {
            $price *= (1 - MINER_STORE_DISCOUNT);
        }

        foreach ($item->required_items as $key => $value) {
            $this->inventoryService->edit($value->name, -$value->amount * $amount);
        }
        Logger::log('item_multiplier' . $item->item_multiplier . ' ' . $amount);
        $this->inventoryService
            ->edit(
                GameConstants::CURRENCY,
                -$this->storeService->calculateItemCost($item->name, $amount)
            )
            ->edit($item->name, $item->item_multiplier * $amount);

        return Response::setStatus(200);
    }
}