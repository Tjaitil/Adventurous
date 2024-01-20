<?php

namespace App\Abstracts;

use App\Http\Builders\StoreBuilder;
use App\Http\Resources\StoreResource;
use Illuminate\Http\JsonResponse;

abstract class AbstractStore
{
    public StoreBuilder $storeBuilder;

    public ?StoreResource $StoreResource = null;

    public function __construct()
    {
        $this->storeBuilder = StoreBuilder::create();
    }

    abstract public function makeStore(array $items = []): StoreResource;

    /**
     * @return StoreResource
     */
    public function getStore()
    {
        if (\is_null($this->StoreResource)) {
            $this->StoreResource = $this->makeStore();
        }

        return $this->StoreResource;
    }

    public function getStoreItemsResponse(): JsonResponse
    {
        return response()->json(['store_items', $this->getStore()->toArray()['store_items']], 200);
    }
}
