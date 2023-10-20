<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $item_id
 * @property string $required_item
 * @property int $amount
 * @property TravelBureauCart $cart
 * @mixin \Eloquent
 */
class TravelBureauCartRequiredItem extends Model
{
    public $timestamps = false;

    public $table = 'travelbureau_carts_req_items';

    public function cart(): BelongsTo
    {
        return $this->belongsTo(TravelBureauCart::class, 'item_id', 'item_id');
    }
}
