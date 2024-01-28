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

    /**
     *
     * @param array<int,mixed> $items
     *
     * @return \App\Http\Resources\StoreResource
     */
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
        $data = ['store_items' => $this->getStore()->toArray()['store_items']];

        return response()->json(['data' => $data], 200);
    }
}
