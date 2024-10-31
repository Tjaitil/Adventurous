<?php

namespace App\Stores;

use App\Abstracts\AbstractStore;
use App\Http\Resources\StoreResource;
use App\Models\ArcheryShopItem;
use App\Services\StoreDiscountService;

class ArcheryStore extends AbstractStore
{
    public function __construct(public StoreDiscountService $storeDiscountService)
    {
        parent::__construct();
    }

    public function makeStore(array $items = []): StoreResource
    {

        $items = ArcheryShopItem::with('requiredItems')
            ->when(count($items) > 0, fn ($query) => $query->whereIn('item', $items))
            ->get();

        $items = $items
            ->sortBy(function ($value, $key) {
                $minLevel = 1;
                $value->skillRequirements->each(function ($skillRequirement) use (&$minLevel) {
                    if ($skillRequirement->level > $minLevel) {
                        $minLevel = $skillRequirement->level;
                    }
                });

                return $minLevel;
            })
            ->sortBy(function ($value, $key) {
                return $value->store_value;
            });

        return $this->StoreResource = $this->storeBuilder::create(['store_items' => $items->toArray()])
            ->setAdjustedStoreValue($this->storeDiscountService->getDiscount('archeryShop'))
            ->setStoreName('archeryShop')
            ->setInfiniteAmount(true)
            ->build();
    }
}
