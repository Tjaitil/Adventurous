<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property string $username
 * @property int $assignment_id
 * @property int $cart_id
 * @property int $cart_amount
 * @property int $delivered
 * @property string|null $trading_countdown
 * @property int $user_id
 * @property int $id
 * @property-read \App\Models\TravelBureauCart|null $cart
 * @property-read \App\Models\TraderAssignment|null $traderAssignment
 * @method static \Database\Factories\TraderFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Trader newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Trader newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Trader query()
 * @method static \Illuminate\Database\Eloquent\Builder|Trader whereAssignmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trader whereCartAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trader whereCartId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trader whereDelivered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trader whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trader whereTradingCountdown($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trader whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Trader whereUsername($value)
 * @mixin \Eloquent
 */
class Trader extends Model
{
    use HasFactory;

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
