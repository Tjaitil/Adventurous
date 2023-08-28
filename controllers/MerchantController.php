<?php

namespace App\controllers;

use Exception;
use App\libs\Request;
use App\libs\Response;
use App\libs\controller;
use App\models\MerchantOffer;
use App\models\Merchant_model;
use App\services\StoreService;
use App\services\SessionService;
use Respect\Validation\Validator;
use App\services\CountdownService;
use App\services\DiplomacyService;
use App\services\InventoryService;
use App\libs\TemplateFetcher;
use App\models\Trader;
use App\models\TraderAssignment;
use App\services\LocationService;
use App\services\SkillsService;
use Carbon\Carbon;

class MerchantController extends controller
{
    public $data;

    function __construct(
        private InventoryService $inventoryService,
        private Merchant_model $merchant_model,
        private StoreService $storeService,
        private SessionService $sessionService,
        private LocationService $locationService,
        private DiplomacyService $diplomacyService,
        private TemplateFetcher $TemplateFetcher,
        private SkillsService $skillsService
    ) {
        parent::__construct();
    }
    public function index(CountdownService $countdownService)
    {
        // $this->data = $this->merchant_model->getData();
        $this->data['offers'] = MerchantOffer::where('location', $this->sessionService->getCurrentLocation())->get()->toArray();

        $this->storeService->makeStore(["list" => $this->data['offers']]);
        $store_items = $this->storeService->storeBuilder->build()->store_items;
        $offers = $store_items;
        if ($this->locationService->isDiplomacyLocation($this->sessionService->getLocation(), false)) {
            foreach ($store_items as $key => $value) {
                $adjusted_price = $this->diplomacyService->calculateNewMerchantPrice(
                    $value->store_value,
                    $this->sessionService->getCurrentLocation()
                );

                $value->adjusted_store_value = ($adjusted_price < $value->merchant_buy_price) ? $value->merchant_buy_price : $adjusted_price;
            }
            $offers = $store_items;
        }

        $this->storeService->storeBuilder->setList($offers);

        $this->storeService->makeStore(["list" => $offers]);
        $this->data['offers'] = $this->storeService->storeBuilder->build()->toArray();


        $Trader = Trader::where('username', $this->sessionService->getCurrentUsername())->first();

        $this->data['trader'] = $Trader;
        $CurrentAssignment = $Trader->traderAssignment;

        // $TraderAssignments = TraderAssignment::where('date_inserted', '>=', Carbon::now()->subHours(4))
        $TraderAssignments = TraderAssignment::with('type')->get();

        $current_location_assignments = [];
        $other_assignments = [];

        $TraderAssignments->each(function (TraderAssignment $assignment) use (&$current_location_assignments, &$other_assignments) {
            if ($assignment->base == $this->sessionService->getCurrentLocation()) {
                $current_location_assignments[] = $assignment;
            } else {
                $other_assignments[] = $assignment;
            }
        });

        // $timestamp = $countdownService->getTimestamp(new DateTime(
        //     $this->data['merchantTimes']['date_inserted']
        // )) + 14400;

        // // If 4 hours has passed, make new trades
        // if ($countdownService->hasTimestampPassed($timestamp)) {
        //     $this->merchant_model->makeTrades();
        // }

        $this->render(
            'merchant',
            'Merchant',
            [
                'CurrentAssignment' => $CurrentAssignment ?? null,
                'current_location' => $this->sessionService->getCurrentLocation(),
                'CurrentLocationAssignments' => $current_location_assignments,
                'OtherAssignments' => $other_assignments,
                'merchant' => $this->data,
                'Trader' => $Trader,
                'offers' => $this->storeService->storeBuilder->build()->toArray(),
                'location' => $this->sessionService->getCurrentLocation(),
                'trader_level' => $this->skillsService->userLevels->trader_level
            ],
            true,
            true
        );
    }

    /**
     * Get price for item
     *
     * @param \App\libs\Request $request
     *
     * @return Response
     */
    public function getPrice(Request $request)
    {
        $item_name = $request->getInput('item');

        $request->validate([
            'item' => Validator::stringVal()->notEmpty(),
        ]);


        $item = MerchantOffer::where('item', $item_name)->first();
        $resource = $this->storeService->createStoreItemResourceFromModel($item);
        $this->storeService->makeStore(["list" => [$resource]]);



        return Response::addData('item', $resource->toArray())->setStatus(200);
    }

    public function trade(Request $request)
    {
        $item = $request->getInput('item');
        $amount = $request->getInput('amount');
        $buy = $request->getInput('buy', 'bool');

        $request->validate([
            'item' => Validator::stringVal()->notEmpty(),
            'amount' => Validator::intVal()->min(0),
            'buy' => Validator::boolVal(),
        ]);

        $item = MerchantOffer::where(
            [
                ['location', $this->sessionService->getLocation()],
                ['item', $item]
            ]
        )->first();

        $item_resource = $this->storeService->createStoreItemResourceFromModel($item);

        $this->storeService->makeStore(["list" => [$item_resource->toArray()]]);
        if (!$this->storeService->isStoreItem($item)) {
            return Response::addMessage("Not a store item")->setStatus(400);
        }

        $store_item = $this->storeService->getStoreItem('item');

        // If user is in a diplomacy location
        if ($this->locationService->isDiplomacyLocation($this->sessionService->getLocation(), false)) {
            $new_diplomacy_merchant_price = $this->diplomacyService->calculateNewMerchantPrice(
                $store_item->store_value,
                $buy,
                $this->sessionService->getLocation()
            );
        } else {
            $new_diplomacy_merchant_price = $store_item->store_value;
        }

        $total_cost = $store_item->store_value * $amount;

        if ($buy) {
            $this->inventoryService->hasEnoughAmount(
                CURRENCY,
                $total_cost
            );

            $this->storeService->hasItemAmount($store_item ?? [], $amount);

            $this->inventoryService->edit(CURRENCY, $total_cost);

            $this->inventoryService->edit($store_item->name, $amount);
        } else {

            if ($this->storeService->isStoreItem($item)) {
                throw new Exception("The merchant isn't interested in what you are trying to sell");
            }

            $this->inventoryService->edit(CURRENCY, $total_cost);

            $this->inventoryService->edit($store_item->name, -$amount);
        }

        // Calculate the new price decrease
        $new_price = $this->storeService->calculateNewPrice($new_diplomacy_merchant_price, $amount, $buy);

        $item->store_value = $new_price;
        $item->amount = $store_item->amount - $amount;
        $item->save();

        if ($this->locationService->isDiplomacyLocation($this->sessionService->getLocation(), false)) {
            // Update diplomacy
            $this->diplomacyService->setNewDiplomacy($this->sessionService->getLocation(), 10);
        }

        $template = $this->TemplateFetcher->loadTemplate(
            'merchantOffers',
            [
                'offers' => $this->storeService->storeBuilder->build(),
                'location' => $this->sessionService->getCurrentLocation()
            ]
        );

        Response::addTemplate('store', $template)->setStatus(200);
    }
}
