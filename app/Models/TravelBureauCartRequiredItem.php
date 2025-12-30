<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property string $required_item
 * @property int $amount
 * @property int|null $item_id
 * @property-read \App\Models\TravelBureauCart|null $cart
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TravelBureauCartRequiredItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TravelBureauCartRequiredItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TravelBureauCartRequiredItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TravelBureauCartRequiredItem whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TravelBureauCartRequiredItem whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TravelBureauCartRequiredItem whereRequiredItem($value)
 * @mixin \Eloquent
 */
class TravelBureauCartRequiredItem extends Model
{
    public $timestamps = false;

    public $table = 'travelbureau_carts_req_items';

    /**
     * @return BelongsTo<TravelBureauCart, $this>
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(TravelBureauCart::class, 'item_id', 'item_id');
    }
}
