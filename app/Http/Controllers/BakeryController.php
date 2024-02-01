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
     * 
     * @return void 
     */
    function __construct(
        protected StoreService $storeService,
        protected BakeryStore $bakeryStore
    ) {
    }

    /**
     * 
     * @return void 
     */
    public function index()
    {
        $storeResource = $this->bakeryStore->getStore();

        return view('bakery')
            ->with('title', 'Bakery')
            ->with('store_resource', $storeResource);
    }

    /**
     * 
     * @return JsonResponse  
     */
    public function getStoreItems()
    {
        return $this->bakeryStore->getStoreItemsResponse();
    }

    /**
     *
     * @return JsonResponse
     */
    public function makeItem(Request $request)
    {
        $item = $request->input('item');
        $amount = $request->integer('amount');

        $initial_store = $this->bakeryStore->makeStore();
        $this->storeService->storeBuilder->setResource($initial_store);

        $result = $this->storeService->buyItem($item, $amount);
        if ($result instanceof JsonResponse) {
            return $result;
        }

        return (new AdvResponse([], 200))->toResponse($request);
    }
}
