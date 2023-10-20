<?php

namespace App\Http\Controllers;

use App\libs\Request;
use App\libs\Response;
use App\libs\controller;
use App\Models\MerchantOffer;
use App\Services\StoreService;
use App\Services\SessionService;
use Respect\Validation\Validator;
use App\Services\DiplomacyService;
use App\Services\InventoryService;
use App\libs\TemplateFetcher;
use App\Models\Item;
use App\Models\Trader;
use App\Models\TraderAssignment;
use App\Services\LocationService;
use App\Services\SkillsService;
use App\Stores\MerchantStore;
use App\validators\ValidateStoreTrade;

class MerchantController extends controller
{
    public $data;

    function __construct(
        private InventoryService $inventoryService,
        private StoreService $storeService,
        private SessionService $sessionService,
        private LocationService $locationService,
        private DiplomacyService $diplomacyService,
        private TemplateFetcher $TemplateFetcher,
        private SkillsService $skillsService,
        private MerchantStore $merchantStore
    ) {
        parent::__construct();
    }
    public function index()
    {
        // $this->data = $this->merchant_model->getData();

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
                'store_resource' => $this->merchantStore->getStore(),
                'location' => $this->sessionService->getCurrentLocation(),
                'trader_level' => $this->skillsService->userLevels->trader_level
            ],
            true,
            true,
            true
        );
    }

    /**
     * 
     * @return Response 
     */
    public function getStoreItems()
    {
        return $this->merchantStore->getStoreItemsResponse();
    }

    /**
     * 
     * @return Response 
     */
    public function getStore()
    {
        $initial_store = $this->merchantStore->makeStore();
        $this->storeService->storeBuilder->setResource($initial_store);

        $store_resource = $this->merchantStore->makeStore();

        $view = $this->viewEngine->render('partials.storeItemListWrapper', [
            'storeItems' => $store_resource->store_items
        ]);

        return Response::addTemplate('storeItemList', $view)
            ->addData('store_items', $store_resource->toArray()['store_items'])
            ->setStatus(200);
    }

    /**
     *
     * 
     * @return Response
     */
    public function getNewStock()
    {
        $store_items = $this->merchantStore->makeStore();
        $view = $this->viewEngine->render('partials.storeItemListWrapper', [
            'store' => $store_items
        ]);

        return $this->merchantStore->getStoreItemsResponse()
            ->addTemplate('storeItemList', $view)
            ->setStatus(200);
    }

    /**
     * Get price for item
     *
     * @param Request $request
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

    /**
     * 
     * @param Request $request 
     * @return Response
     */
    public function openTrade(Request $request)
    {
        $item = $request->getInput('item');
        $amount = $request->getInput('amount');
        ValidateStoreTrade::validate($request);

        if ($this->sessionService->getCurrentLocation() === 'fagna') {
            $Item = Item::where('name', $item)->first();
            $isSellingOtherItem = true;
            if (!$Item instanceof Item) {
                return Response::addMessage("The merchant is not interested in that item")->setStatus(400);
            }
            $total_cost = $Item->store_value * $amount;
        }

        if ($this->inventoryService->hasEnoughAmount(CURRENCY, $total_cost)) {
            return $this->inventoryService->logNotEnoughAmount(CURRENCY);
        }

        $this->inventoryService->edit(CURRENCY, -$total_cost);

        return Response::setStatus(200);
    }

    /**
     * 
     * @param Request $request 
     * @return Response
     */
    public function trade(Request $request)
    {
        $item = $request->getInput('item');
        $amount = $request->getInput('amount');
        $isBuying = $request->getInput('isBuying', 'bool');

        $request->validate([
            'item' => Validator::stringVal()->notEmpty(),
            'amount' => Validator::intVal()->min(0),
            'isBuying' => Validator::boolVal(),
        ]);

        $initial_store = $this->merchantStore->makeStore();
        $this->storeService->storeBuilder->setResource($initial_store);

        $MerchantOffer = MerchantOffer::where('item', $item)
            ->where('location', $this->sessionService->getCurrentLocation())
            ->first();

        if (!$MerchantOffer instanceof MerchantOffer) {
            return Response::addMessage("The merchant is not interested in that item")->setStatus(400);
        }

        $store_item = $this->storeService->getStoreItem($item);

        $total_cost = $store_item->store_value * $amount;

        if ($isBuying) {
            if (!$this->inventoryService->hasEnoughAmount(
                CURRENCY,
                $total_cost
            )) {
                return Response::addMessage("You don't have enough gold")->setStatus(400);
            }

            if (!$this->storeService->hasItemAmount($store_item, $amount)) {
                return Response::addMessage("The merchant isn't selling that many item")->setStatus(400);
            }

            $this->inventoryService->edit(CURRENCY, -$total_cost);

            $this->inventoryService->edit($store_item->name, $amount);
            $MerchantOffer->amount = $store_item->amount - $amount;
        } else {
            if (!$this->inventoryService->hasEnoughAmount($item, $amount)) {
                return Response::addMessage("You don't have enough of that item")->setStatus(400);
            }

            $this->inventoryService->edit(CURRENCY, $total_cost);

            $this->inventoryService->edit($store_item->name, -$amount);
            $MerchantOffer->amount = $store_item->amount + $amount;
        }

        // Calculate the new price decrease
        $new_price = $this->storeService->calculateNewPrice($MerchantOffer->store_value, $amount, $isBuying);
        $MerchantOffer->store_value = $new_price;
        $MerchantOffer->store_buy_price = floor($new_price * 0.97);
        $MerchantOffer->save();

        // Update price if diplomacy location
        if ($this->locationService->isDiplomacyLocation($this->sessionService->getLocation(), false)) {
            $this->diplomacyService->setNewDiplomacy($this->sessionService->getLocation(), 10);
        }

        $store_resource = $this->merchantStore->makeStore();

        $view = $this->viewEngine->render('partials.storeItemListWrapper', [
            'storeItems' => $store_resource->store_items
        ]);

        return Response::addTemplate('storeItemList', $view)
            ->addData('store_items', $store_resource->toArray()['store_items'])
            ->setStatus(200);
    }
}
