<?php

namespace App\models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 * @property int $id
 * @property string $username
 * @property Carbon $trading_countdown
 * @property int $delivered
 * @property int $cart_amount
 * @property string $cart
 * @property int $assignment_id
 * @property TraderAssignment $traderAssignment
 * @property TravelBureauCart $cartData
 * @mixin \Eloquent
 */
class Trader extends Model
{
    protected $guarded = [];

    public $table = 'trader';

    public $timestamps = false;

    public function traderAssignment()
    {
        return $this->hasOne(TraderAssignment::class, 'id', 'assignment_id');
    }

    public function cartData()
    {
        return $this->hasOne(TravelBureauCart::class, 'item', 'cart');
    }
}
