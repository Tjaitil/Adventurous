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
 * @property int $assignment_id
 * @property int $cart_id
 * @property TraderAssignment $traderAssignment
 * @property TravelBureauCart $cart
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

    public function cart()
    {
        return $this->hasOne(TravelBureauCart::class, 'id', 'cart_id');
    }
}
