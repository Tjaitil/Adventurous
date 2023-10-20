<?php

namespace App\Stores;

use App\Abstracts\AbstractStore;
use App\Models\Item;
use App\Http\Resources\StoreResource;

class ZinssStore extends AbstractStore
{

    public function __construct()
    {
        parent::__construct();
    }

    public function makeStore(array $items = []): StoreResource
    {
        $items = Item::whereIn('name', ['daqloon horns', 'daqloon scale'])->get();

        $this->StoreResource = $this->storeBuilder::create(["store_items" => $items])
            ->setStoreName('zinsstore')
            ->setInfiniteAmount(true)
            ->build();

        return $this->StoreResource;
    }
}
