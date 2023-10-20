<?php

namespace App\Stores;

use App\Abstracts\AbstractStore;
use App\Models\TravelBureauCart;
use App\Http\Resources\StoreResource;
use Illuminate\Database\Eloquent\Builder;

class TravelBureauStore extends AbstractStore
{
    public function __construct()
    {
        parent::__construct();
    }

    public function makeStore(array $items = []): StoreResource
    {
        $items = TravelBureauCart::with('requiredItems', 'skillRequirements')
            ->when(count($items) > 0, fn (Builder $query) => $query->whereIn('name', $items))
            ->get();

        return $this->StoreResource = $this->storeBuilder::create(['store_items' => $items])
            ->setInfiniteAmount(true)
            ->build();
    }
}
