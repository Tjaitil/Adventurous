<?php

namespace App\Http\Controllers;

use App\Http\Responses\AdvResponse;
use App\Services\StoreService;
use App\Stores\ArcheryStore;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArcheryShopController extends Controller
{
    public function __construct(
        private StoreService $storeService,
        private ArcheryStore $archeryStore,
    ) {
    }

    /**
     * @return View|Factory
     */
    public function index()
    {
        $storeResource = $this->archeryStore->makeStore();

        return view('archeryshop')
            ->with('title', 'Archery Shop')
            ->with('isDiscountActive', $storeResource->store_value_modifier !== 1.00)
            ->with('store_resource', $storeResource);
    }

    public function getStoreItems(): JsonResponse
    {
        return $this->archeryStore->getStoreItemsResponse();
    }

    /**
     * @return JsonResponse|AdvResponse
     */
    public function fletchItem(Request $request)
    {
        $item = $request->input('item');
        $amount = $request->integer('amount');

        $initial_store = $this->archeryStore->makeStore();
        $this->storeService->storeBuilder->setResource($initial_store);

        $result = $this->storeService->buyItem($item, $amount);
        if (! is_array($result)) {
            return $result;
        } else {
            $message = sprintf('%d x %s fletched for %d {gold}', $result['totalAmount'], $item, $result['totalPrice']);

            return advResponse()->addSuccessMessage($message);
        }
    }
}
