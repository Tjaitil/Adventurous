<?php

namespace App\Http\Controllers;

use App\Http\Responses\AdvResponse;
use App\Models\User;
use App\Services\GameLogService;
use App\Services\StoreService;
use App\Stores\ZinssStore;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ZinsstoreController extends Controller
{
    public function __construct(
        private ZinssStore $zinssStore,
        private StoreService $storeService,
    ) {}

    /**
     * @return View|Factory
     */
    public function index(#[CurrentUser] User $User)
    {
        $storeResource = $this->zinssStore->makeStore($User);

        return view('zinsstore')
            ->with('title', 'Zins Store')
            ->with('store_resource', $storeResource);
    }

    /**
     * @return JsonResponse
     */
    public function getStoreItems(#[CurrentUser] User $User)
    {
        $store = $this->zinssStore->makeStore($User);

        return $this->zinssStore->toStoreItemResponse($store);
    }

    /**
     * @return JsonResponse|AdvResponse
     */
    public function sellItem(#[CurrentUser] User $User, Request $request)
    {
        $item = $request->input('item');
        $amount = $request->integer('amount');

        $initial_store = $this->zinssStore->makeStore($User, [$item]);
        $this->storeService->storeBuilder->setResource($initial_store);
        $result = $this->storeService->sellItem($User->inventory, $item, $amount, $User->id);
        if (! is_array($result)) {
            return $result;
        } else {
            $successMessage = sprintf('%d x %s sold for %d', $amount, $item, $result['totalPrice']);

            return advResponse()->addMessage(GameLogService::addSuccessLog($successMessage));
        }
    }
}
