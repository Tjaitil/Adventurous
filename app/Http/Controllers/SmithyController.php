<?php

namespace App\Http\Controllers;

use App\Http\Responses\AdvResponse;
use App\Services\GameLogService;
use App\Services\StoreService;
use App\Stores\SmithyStore;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SmithyController extends Controller
{
    public function __construct(
        protected StoreService $storeService,
        protected SmithyStore $smithyStore,
    ) {}

    /**
     * @return View|Factory
     */
    public function index()
    {
        $storeResource = $this->smithyStore->makeStore();

        return view('smithy')
            ->with('title', 'Smithy')
            ->with('isDiscountActive', $storeResource->store_value_modifier !== 1.00)
            ->with('store_resource', $storeResource);
    }

    public function getStoreItems(): JsonResponse
    {
        return $this->smithyStore->getStoreItemsResponse();
    }

    /**
     * @return JsonResponse|AdvResponse
     */
    public function smithItem(Request $request)
    {
        $item = $request->input('item');
        $amount = $request->integer('amount');

        $initial_store = $this->smithyStore->makeStore();
        $this->storeService->storeBuilder->setResource($initial_store);

        $result = $this->storeService->buyItem($item, $amount);
        if (! is_array($result)) {
            return $result;
        } else {
            $message = sprintf('%d x %s smithed for %d {gold}', $result['totalAmount'], $item, $result['totalPrice']);

            return advResponse()->addMessage(
                GameLogService::addSuccessLog($message));
        }

    }
}
