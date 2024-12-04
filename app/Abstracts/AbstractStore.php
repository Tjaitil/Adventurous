<?php

namespace App\Abstracts;

use App\Http\Builders\StoreBuilder;
use App\Http\Resources\StoreResource;
use App\Models\User;
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
     * @param  array<int,mixed>  $items
     */
    abstract public function makeStore(User $User, array $items = []): StoreResource;

    public function toStoreItemResponse(StoreResource $StoreResource): JsonResponse
    {
        $data = ['store_items' => $StoreResource->store_items];

        return response()->json(['data' => $data], 200);
    }
}
