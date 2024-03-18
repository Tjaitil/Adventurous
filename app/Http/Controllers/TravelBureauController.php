<?php

namespace App\Http\Controllers;

use App\Exceptions\JsonException;
use App\Http\Responses\AdvResponse;
use App\Models\Trader;
use App\Models\TravelBureauCart;
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
     * @return JsonResponse|AdvResponse
     *
     * @throws \Exception|JsonException
     */
    public function buyCart(Request $request)
    {
        $item = $request->string('item');

        $Trader = Trader::where('username', $this->sessionService->getCurrentUsername())->first();
        $Cart = TravelBureauCart::where('name', $item)->first();
        if (! $Trader instanceof Trader || ! $Cart instanceof TravelBureauCart) {
            throw new JsonException('Could not find trader or cart: '.$item);
        }

        if ($Cart->id === $Trader->cart_id) {
            return (new AdvResponse([], 400))
                ->addErrorMessage('You already have this cart')
                ->toResponse($request);
        }

        $initial_store = $this->travelBureauStore->makeStore([$item]);
        $this->storeService->storeBuilder->setResource($initial_store);

        $result = $this->storeService->buyItem($item, 1);
        if ($result !== true) {
            return $result;
        }

        $Trader->cart_id = $Cart->id;
        $Trader->save();

        return (new AdvResponse)->setStatus(200)
            ->addInfoMessage(sprintf('You bought %s', $item))
            ->addData('new_cart', $item)
            ->toResponse($request);
    }
}
