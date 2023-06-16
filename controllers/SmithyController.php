<?php

namespace App\controllers;

use App\actions\MapRequiredDataAction;
use App\libs\controller;
use App\libs\Logger;
use App\libs\Request;
use App\libs\Response;
use App\models\SmithyItem;
use App\services\InventoryService;
use App\services\SessionService;
use App\services\SkillsService;
use App\services\StoreService;
use App\validators\ValidateStoreTrade;
use GameConstants;

class SmithyController extends controller
{
    protected $discount = 0;

    function __construct(
        protected StoreService $storeService,
        protected InventoryService $inventoryService,
        protected SessionService $sessionService,
        protected SmithyItem $SmithyItem,
        protected MapRequiredDataAction $mapRequiredDataAction
    ) {
        parent::__construct();
    }

    public function index()
    {

        $data['store_items'] = $this->makeShop();
        $data['discount'] = $this->discount = $this->sessionService->isProfiency(GameConstants::MINER_SKILL_NAME) ? GameConstants::MINER_STORE_DISCOUNT * 100 : 0;

        $this->render('smithy', 'Smithy', $data, true, true);
    }

    protected function makeShop()
    {
        $SmithyItems = $this->SmithyItem->with('requiredItems', 'skillRequirements')->get();

        $item = $SmithyItems->sortBy(function ($value, $key) {
            $minLevel = 1;
            $value->skillRequirements->each(function ($skillRequirement) use (&$minLevel) {
                if ($skillRequirement->level > $minLevel) {
                    $minLevel = $skillRequirement->level;
                }
            });
            return $minLevel;
        });

        $this->storeService->makeStore(
            ["list" => $item]
        );

        if ($this->sessionService->isProfiency(GameConstants::MINER_SKILL_NAME)) {
            $this->storeService->storeBuilder->setAdjustedStoreValue(GameConstants::MINER_STORE_DISCOUNT);
        }

        $this->storeService->storeBuilder->setInfiniteAmount(true);

        return $this->storeService->storeBuilder->build()->toArray();
    }

    public function get()
    {
        return Response::addData("store_items", $this->makeShop())->setStatus(200);
    }

    /**
     * Smith item
     *
     * @param Request $request
     * @param MapRequiredDataAction $mapRequiredDataAction
     * @param SkillsService $skillsService
     *
     * @return Response
     */
    public function smithItem(Request $request, SkillsService $skillsService)
    {

        $item = $request->getInput('item');
        $amount = $request->getInput('amount');

        ValidateStoreTrade::validate($request);

        $item_data = $this->SmithyItem->with('requiredItems', 'skillRequirements')->where('item', $item)->get();

        $this->storeService->makeStore(
            ["list" => $item_data]
        );


        if (!$this->storeService->isStoreItem($item)) {
            return $this->storeService->logNotStoreItem($item);
        }

        $item = $this->storeService->getStoreItem($item);

        // Check that user has required level
        if (!$this->storeService->hasSkillRequirements($item->name)) {
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

        $this->inventoryService
            ->edit(
                CURRENCY,
                -$this->storeService->calculateItemCost($item->name, $amount)
            )
            ->edit($item->name, $item->item_multiplier * $amount);

        return Response::setStatus(200);
    }
}
