<?php

namespace App\Http\Controllers;

use App\Exceptions\JsonException;
use App\Http\Responses\AdvResponse;
use App\Models\Trader;
use App\Models\TravelBureauCart;
use App\Models\User;
use App\Services\GameLogService;
use App\Services\SessionService;
use App\Services\StoreService;
use App\Stores\TravelBureauStore;
use Illuminate\Container\Attributes\CurrentUser;
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
    ) {}

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index(#[CurrentUser] User $User)
    {
        $current_cart = Trader::where('username', $this->sessionService->getCurrentUsername())->first()?->cart;
        $store_resource = $this->travelBureauStore->makeStore($User);

        return view('travelbureau')
            ->with('title', 'Travel Bureau')
            ->with('store_resource', $store_resource)
            ->with('current_cart', $current_cart);
    }

    /**
     * @return JsonResponse
     */
    public function getStoreItems(#[CurrentUser] User $User)
    {
        $store = $this->travelBureauStore->makeStore($User);

        return $this->travelBureauStore->toStoreItemResponse($store);
    }

    /**
     * @return JsonResponse|AdvResponse
     *
     * @throws \Exception|JsonException
     */
    public function buyCart(#[CurrentUser] User $User, Request $request)
    {
        $item = $request->string('item');

        $Trader = Trader::where('username', $this->sessionService->getCurrentUsername())->first();
        $Cart = TravelBureauCart::where('name', $item)->first();
        if (! $Trader instanceof Trader || ! $Cart instanceof TravelBureauCart) {
            throw new JsonException('Could not find trader or cart: '.$item);
        }

        if ($Cart->id === $Trader->cart_id) {
            return (new AdvResponse([], 400))
                ->addMessage(GameLogService::addErrorLog('You already have this cart'))
                ->toResponse($request);
        }

        $initial_store = $this->travelBureauStore->makeStore($User, [$item]);
        $this->storeService->storeBuilder->setResource($initial_store);

        $result = $this->storeService->buyItem($User->inventory, $item, 1, $User->id);
        if ($result instanceof JsonResponse || $result instanceof AdvResponse) {
            return $result;
        }

        $Trader->cart_id = $Cart->id;
        $Trader->save();

        return (new AdvResponse)->setStatus(200)
            ->addMessage(GameLogService::addInfoLog(sprintf('You bought %s', $item)))
            ->addData('new_cart', $item)
            ->toResponse($request);
    }
}
