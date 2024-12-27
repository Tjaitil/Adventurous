<?php

namespace App\Services;

use App\Models\StoreDiscount;
use App\Models\User;

class StoreDiscountService
{
    public function __construct(public SessionService $sessionService) {}

    /**
     * @return float
     */
    public function getDiscount(string $storeName, User $User)
    {
        $StoreDiscount = StoreDiscount::where('store', $storeName)->first();
        if (! $StoreDiscount instanceof StoreDiscount) {
            return 1.00;
        } elseif ($User->player->profiency === $StoreDiscount->profiency) {
            return $StoreDiscount->discount;
        } else {
            return 1.00;
        }
    }
}
