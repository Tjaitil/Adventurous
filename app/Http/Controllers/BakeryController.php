<?php

namespace App\Http\Controllers;

use App\Http\Responses\AdvResponse;
use App\Models\User;
use App\Services\GameLogService;
use App\Services\StoreService;
use App\Stores\BakeryStore;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BakeryController extends Controller
{
    /**
     * @return void
     */
    public function __construct(
        protected StoreService $storeService,
        protected BakeryStore $bakeryStore
    ) {}

    /**
     * @return void
     */
    public function index(#[CurrentUser] User $User)
    {
        $storeResource = $this->bakeryStore->makeStore($User);

        return view('bakery')
            ->with('title', 'Bakery')
            ->with('isDiscountActive', $storeResource->store_value_modifier !== 1.00)
            ->with('store_resource', $storeResource);
    }

    /**
     * @return JsonResponse
     */
    public function getStoreItems(#[CurrentUser] User $User)
    {
        $store = $this->bakeryStore->makeStore($User);

        return $this->bakeryStore->toStoreItemResponse($store);
    }

    /**
     * @return JsonResponse|AdvResponse
     */
    public function makeItem(#[CurrentUser] User $User, Request $request)
    {
        $item = $request->input('item');
        $amount = $request->integer('amount');

        $initial_store = $this->bakeryStore->makeStore($User);
        $this->storeService->storeBuilder->setResource($initial_store);

        $result = $this->storeService->buyItem($User->inventory, $item, $amount, $User->id);
        if (! is_array($result)) {
            return $result;
        } else {
            $message = sprintf('%d x %s made for %d {gold}', $result['totalAmount'], $item, $result['totalPrice']);

            return advResponse()->addMessage(
                GameLogService::addSuccessLog($message));
        }
    }
}
