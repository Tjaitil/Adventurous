<?php

namespace App\Stores;

use App\Abstracts\AbstractStore;
use App\models\MerchantOffer;
use App\resources\StoreResource;
use App\services\DiplomacyService;
use App\services\LocationService;
use App\services\SessionService;
use Illuminate\Database\Eloquent\Builder;

class MerchantStore extends AbstractStore
{

    public function __construct(protected SessionService $sessionService, protected LocationService $locationService, protected DiplomacyService $diplomacyService)
    {
        parent::__construct();
    }

    public function makeStore(array $items = []): StoreResource
    {

        $items = MerchantOffer::where('location', $this->sessionService->getCurrentLocation())
            ->when(count($items) > 0, fn (Builder $query) => $query->whereIn('item', $items))
            ->get();

        $this->storeBuilder = $this->storeBuilder::create(['store_items' => $items])->setStoreName('merchant');

        $this->adjustPriceWhenDiplomacy();
        return $this->StoreResource = $this->storeBuilder->build();
    }

    public function adjustPriceWhenDiplomacy()
    {
        $store_items = $this->storeBuilder->build()->store_items;
        if ($this->locationService->isDiplomacyLocation($this->sessionService->getLocation(), false)) {
            foreach ($store_items as $key => $value) {
                $adjusted_price = $this->diplomacyService->calculateNewMerchantPrice(
                    $value->store_value,
                    $this->sessionService->getCurrentLocation()
                );
                $adjusted_store_value = ($adjusted_price < $value->store_buy_price) ? $value->store_buy_price : $adjusted_price;
                $adjusted_store_buy_price = floor($adjusted_store_value * 0.97);
                $this->storeBuilder->setAdjustedStoreValueForItem($value->name, $adjusted_store_value);
                $this->storeBuilder->setStoreBuyPriceForItem($value->name, $adjusted_store_buy_price);
            }
        }
    }
}
