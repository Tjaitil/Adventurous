<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * 
 *
 * @property int $id
 * @property string $username
 * @property int $user_id
 * @property int $cart_id
 * @property int $cart_amount
 * @property int $delivered
 * @property string|null $trading_countdown
 * @property int $assignment_id
 * @property-read \App\Models\TravelBureauCart|null $cart
 * @property-read \App\Models\TraderAssignment|null $traderAssignment
 * @method static \Database\Factories\TraderFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trader newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trader newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trader query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trader whereAssignmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trader whereCartAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trader whereCartId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trader whereDelivered($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trader whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trader whereTradingCountdown($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trader whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Trader whereUsername($value)
 * @mixin \Eloquent
 */
class Trader extends Model
{
    /**
     * @use HasFactory<\Database\Factories\TraderFactory>
     */
    use HasFactory;

    protected $guarded = [];

    public $table = 'trader';

    public $timestamps = false;

    /**
     * @return HasOne<TraderAssignment, $this>
     */
    public function traderAssignment(): HasOne
    {
        return $this->hasOne(TraderAssignment::class, 'id', 'assignment_id');
    }

    /**
     * @return HasOne<TravelBureauCart, $this>
     */
    public function cart(): HasOne
    {
        return $this->hasOne(TravelBureauCart::class, 'id', 'cart_id');
    }
}
