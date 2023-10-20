<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $item_id
 * @property string $required_item
 * @property string $amount
 * @property HealingItem $healing_item
 * @mixin \Eloquent
 */
class HealingItemRequired extends Model
{
    public $timestamps = false;
    public $table = 'healing_items_required';

    public function healingItem()
    {
        return $this->belongsTo(HealingItem::class);
    }
}
