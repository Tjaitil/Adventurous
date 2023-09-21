<?php

namespace App\Abstracts;

use App\builders\StoreBuilder;
use App\libs\Response;
use App\resources\StoreResource;

abstract class AbstractStore
{
    public StoreBuilder $storeBuilder;
    public ?StoreResource $StoreResource = null;

    public function __construct()
    {
        $this->storeBuilder = StoreBuilder::create();
    }

    public abstract function makeStore(array $items = []): StoreResource;

    /**
     * 
     * @return StoreResource 
     */
    public function getStore()
    {
        if (\is_null($this->StoreResource)) {
            $this->StoreResource = $this->makeStore();
        }

        return $this->StoreResource;
    }

    /**
     * 
     * @return \App\libs\Response 
     */
    public function getStoreItemsResponse(): Response
    {
        return Response::addData("store_items", $this->getStore()->toArray()['store_items'])->setStatus(200);
    }
}
