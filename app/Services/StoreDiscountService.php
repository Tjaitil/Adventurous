<?php

namespace App\services;

use App\models\StoreDiscount;

class StoreDiscountService
{
    public function __construct(public SessionService $sessionService)
    {
    }


    /**
     * 
     * @param string $storeName 
     * @return float 
     */
    public function getDiscount(string $storeName)
    {
        $StoreDiscount = StoreDiscount::where('store', $storeName)->first();
        if (\is_null($StoreDiscount) || !$StoreDiscount instanceof StoreDiscount) {
            return 1.00;
        } else if ($this->sessionService->isProfiency($StoreDiscount->profiency)) {
            return $StoreDiscount->discount;
        } else {
            return 1.00;
        }
    }
}
