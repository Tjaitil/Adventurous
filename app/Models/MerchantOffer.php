<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * 
 *
 * @property int $id
 * @property string $location
 * @property string $item
 * @property int $store_value
 * @property int $store_buy_price
 * @property int $amount
 * @property string $date_inserted
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MerchantOffer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MerchantOffer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MerchantOffer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MerchantOffer whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MerchantOffer whereDateInserted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MerchantOffer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MerchantOffer whereItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MerchantOffer whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MerchantOffer whereStoreBuyPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MerchantOffer whereStoreValue($value)
 * @mixin \Eloquent
 */
class MerchantOffer extends Model
{
    public $timestamps = false;

    protected $guarded = [];
    protected $dates = ['date_inserted'];
}
