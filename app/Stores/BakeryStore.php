<?php

namespace App\Stores;

use App\Abstracts\AbstractStore;
use App\models\HealingItem;
use App\resources\StoreResource;
use App\services\StoreDiscountService;
use Illuminate\Database\Eloquent\Builder;

class BakeryStore extends AbstractStore
{

    public function __construct(
        public StoreDiscountService $storeDiscountService,
    ) {
        parent::__construct();
    }

    public function makeStore(array $items = []): StoreResource
    {
        $items = HealingItem::with('requiredItems')
            ->when(count($items) > 0, fn (Builder $query) => $query->whereIn('item', $items))
            ->where('bakery_item', 1)->get();

        return $this->StoreResource = $this->storeBuilder::create(['store_items' => $items])
            ->setAdjustedStoreValue($this->storeDiscountService->getDiscount('bakery'))
            ->setStoreName('bakery')
            ->setInfiniteAmount(true)
            ->build();
    }
}
