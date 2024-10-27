<?php

namespace App\Http\Controllers;

use App\Http\Responses\AdvResponse;
use App\Services\GameLogService;
use App\Services\StoreService;
use App\Stores\ZinssStore;
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
    public function index()
    {
        $storeResource = $this->zinssStore->getStore();

        return view('zinsstore')
            ->with('title', 'Zins Store')
            ->with('store_resource', $storeResource);
    }

    /**
     * @return JsonResponse
     */
    public function getStoreItems()
    {
        return $this->zinssStore->getStoreItemsResponse();
    }

    /**
     * @return JsonResponse|AdvResponse
     */
    public function sellItem(Request $request)
    {
        $item = $request->input('item');
        $amount = $request->integer('amount');

        $initial_store = $this->zinssStore->makeStore([$item]);
        $this->storeService->storeBuilder->setResource($initial_store);
        $result = $this->storeService->sellItem($item, $amount);
        if (! is_array($result)) {
            return $result;
        } else {
            $successMessage = sprintf('%d x %s sold for %d', $amount, $item, $result['totalPrice']);

            return advResponse()->addMessage(GameLogService::addSuccessLog($successMessage));
        }
    }
}
