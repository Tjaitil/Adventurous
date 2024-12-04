<?php

namespace App\Stores;

use App\Abstracts\AbstractStore;
use App\Http\Resources\StoreResource;
use App\Models\HealingItem;
use App\Models\User;
use App\Services\StoreDiscountService;

class BakeryStore extends AbstractStore
{
    public function __construct(
        public StoreDiscountService $storeDiscountService,
    ) {
        parent::__construct();
    }

    public function makeStore(User $User, array $items = []): StoreResource
    {
        $items = HealingItem::with('requiredItems')
            ->when(count($items) > 0, fn ($query) => $query->whereIn('item', $items))
            ->where('bakery_item', 1)->get();

        return $this->StoreResource = $this->storeBuilder::create(['store_items' => $items->toArray()])
            ->setAdjustedStoreValue($this->storeDiscountService->getDiscount('bakery', $User))
            ->setStoreName('bakery')
            ->setInfiniteAmount(true)
            ->build();
    }
}
