<?php

namespace App\Http\Controllers;

use App\Http\Responses\AdvResponse;
use App\Models\User;
use App\Services\GameLogService;
use App\Services\StoreService;
use App\Stores\ArcheryStore;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArcheryShopController extends Controller
{
    public function __construct(
        private StoreService $storeService,
        private ArcheryStore $archeryStore,
    ) {}

    /**
     * @return View|Factory
     */
    public function index(#[CurrentUser] User $User)
    {
        $storeResource = $this->archeryStore->makeStore($User, []);

        return view('archeryshop')
            ->with('title', 'Archery Shop')
            ->with('isDiscountActive', $storeResource->store_value_modifier !== 1.00)
            ->with('store_resource', $storeResource);
    }

    public function getStoreItems(#[CurrentUser] User $User): JsonResponse
    {
        $store = $this->archeryStore->makeStore($User);

        return $this->archeryStore->toStoreItemResponse($store);
    }

    /**
     * @return JsonResponse|AdvResponse
     */
    public function fletchItem(#[CurrentUser] User $User, Request $request)
    {
        $item = $request->input('item');
        $amount = $request->integer('amount');

        $initial_store = $this->archeryStore->makeStore($User);
        $this->storeService->storeBuilder->setResource($initial_store);

        $result = $this->storeService->buyItem($User->inventory, $item, $amount, $User->id);
        if (! is_array($result)) {
            return $result;
        } else {
            $message = sprintf('%d x %s fletched for %d {gold}', $result['totalAmount'], $item, $result['totalPrice']);

            return advResponse()->addMessage(
                GameLogService::addSuccessLog($message));
        }
    }
}
