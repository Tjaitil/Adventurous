<?php

namespace App\Http\Controllers;

use App\Http\Responses\AdvResponse;
use App\Services\StoreService;
use App\Stores\BakeryStore;
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
    ) {
    }

    /**
     * @return void
     */
    public function index()
    {
        $storeResource = $this->bakeryStore->getStore();

        return view('bakery')
            ->with('title', 'Bakery')
            ->with('isDiscountActive', $storeResource->store_value_modifier !== 1.00)
            ->with('store_resource', $storeResource);
    }

    /**
     * @return JsonResponse
     */
    public function getStoreItems()
    {
        return $this->bakeryStore->getStoreItemsResponse();
    }

    /**
     * @return JsonResponse|AdvResponse
     */
    public function makeItem(Request $request)
    {
        $item = $request->input('item');
        $amount = $request->integer('amount');

        $initial_store = $this->bakeryStore->makeStore();
        $this->storeService->storeBuilder->setResource($initial_store);

        $result = $this->storeService->buyItem($item, $amount);
        if (! is_array($result)) {
            return $result;
        } else {
            $message = sprintf('%d x %s made for %d {gold}', $result['totalAmount'], $item, $result['totalPrice']);

            return advResponse()->addSuccessMessage($message);
        }
    }
}
