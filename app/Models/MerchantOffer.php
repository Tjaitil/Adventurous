<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * @property int $id
 * @property string $location
 * @property string $item
 * @property int $store_value
 * @property int $store_buy_price
 * @property int $amount
 * @property Carbon $date_inserted
 * @mixin \Eloquent
 */
class MerchantOffer extends Model
{
    public $timestamps = false;

    protected $guarded = [];
    protected $dates = ['date_inserted'];
}
