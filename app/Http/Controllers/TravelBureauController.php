<?php

namespace App\Http\Controllers;

use App\Exceptions\JsonException;
use App\Http\Responses\AdvResponse;
use App\Models\Trader;
use App\Models\TravelBureauCart;
use App\Services\InventoryService;
use App\Services\SessionService;
use App\Services\StoreService;
use App\Stores\TravelBureauStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TravelBureauController extends Controller
{
    /**
     * @return void
     */
    public function __construct(
        private StoreService $storeService,
        private InventoryService $inventoryService,
        private SessionService $sessionService,
        private TravelBureauStore $travelBureauStore
    ) {
    }

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        $current_cart = Trader::where('username', $this->sessionService->getCurrentUsername())->first()?->cart;
        $store_resource = $this->travelBureauStore->getStore();

        return view('travelbureau')
            ->with('title', 'Travel Bureau')
            ->with('store_resource', $store_resource)
            ->with('current_cart', $current_cart);
    }

    /**
     * @return JsonResponse
     */
    public function getStoreItems()
    {
        return $this->travelBureauStore->getStoreItemsResponse();
    }

    /**
     * @return JsonResponse
     *
     * @throws \Exception|JsonException
     */
    public function buyCart(Request $request)
    {
        $item = $request->string('item');
        $amount = 1;
        $initial_store = $this->travelBureauStore->makeStore([$item]);
        $this->storeService->storeBuilder->setResource($initial_store);

        $Trader = Trader::where('username', $this->sessionService->getCurrentUsername())->first();
        $Cart = TravelBureauCart::where('name', $item)->first();
        if (! $Trader instanceof Trader || ! $Cart instanceof TravelBureauCart) {
            throw new JsonException('Could not find trader or cart: ' . $item);
        }

        if (! $this->storeService->isStoreItem($item)) {
            return $this->storeService->logNotStoreItem($item);
        }
        $store_item = $this->storeService->getStoreItem($item);

        if ($Cart->id === $Trader->cart_id) {
            return (new AdvResponse([], 422))
                ->addErrorMessage('You already have this cart')
                ->toResponse($request);
        }

        if($this->storeService->hasSkillRequirements($item)) {
            return $this->storeService->logHasntSkillRequirements();
        }

        foreach ($store_item->required_items as $key => $value) {
            if (! $this->inventoryService->hasEnoughAmount(
                $value->name,
                $value->amount * $amount
            )) {
                return $this->inventoryService->logNotEnoughAmount($value->name);
            }
        }

        foreach ($store_item->required_items as $key => $value) {
            $this->inventoryService->edit($value->name, $value->amount * $amount);
        }

        if (! $this->inventoryService->hasEnoughAmount(config('adventurous.currency'), $store_item->store_value)) {
            return $this->inventoryService->logNotEnoughAmount(config('adventurous.currency'));
        } else {

            $this->inventoryService
                ->edit(
                    config('adventurous.currency'),
                    -$this->storeService->calculateItemCost($store_item->name, $amount)
                );
        }

        $Trader->cart_id = $Cart->id;
        $Trader->save();

        return (new AdvResponse)->setStatus(200)
            ->addInfoMessage(sprintf('You bought %s', $item))
            ->addData('new_cart', $item)
            ->toResponse($request);
    }
}
