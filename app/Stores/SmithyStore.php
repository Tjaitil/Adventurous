<?php

namespace App\Stores;

use App\Abstracts\AbstractStore;
use App\Http\Resources\StoreResource;
use App\Models\SmithyItem;
use App\Services\StoreDiscountService;

class SmithyStore extends AbstractStore
{
    public function __construct(
        public StoreDiscountService $storeDiscountService
    ) {
        parent::__construct();
    }

    public function makeStore(array $items = []): StoreResource
    {
        $items = SmithyItem::with('requiredItems')
            ->when(count($items) > 0, fn ($query) => $query->whereIn('item', $items))
            ->get();

        $items = $items->sortBy(function ($value, $key) {
            $minLevel = 1;
            $value->skillRequirements->each(function ($skillRequirement) use (&$minLevel) {
                if ($skillRequirement->level > $minLevel) {
                    $minLevel = $skillRequirement->level;
                }
            });

            return $minLevel;
        });

        return $this->StoreResource = $this->storeBuilder::create(['store_items' => $items->toArray()])
            ->setAdjustedStoreValue($this->storeDiscountService->getDiscount('smithy'))
            ->setStoreName('smithy')
            ->setInfiniteAmount(true)
            ->build();
    }
}
