<?php

namespace App\Stores;

use App\Abstracts\AbstractStore;
use App\Http\Resources\StoreResource;
use App\Models\TravelBureauCart;

class TravelBureauStore extends AbstractStore
{
    public function __construct()
    {
        parent::__construct();
    }

    public function makeStore(array $items = []): StoreResource
    {
        $items = TravelBureauCart::with('requiredItems', 'skillRequirements')
            ->when(count($items) > 0, fn ($query) => $query->whereIn('name', $items))
            ->get();

        return $this->StoreResource = $this->storeBuilder::create(['store_items' => $items->toArray()])
            ->setInfiniteAmount(true)
            ->setInventorable(false)
            ->build();
    }
}
