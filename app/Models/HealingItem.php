<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $item_id
 * @property int $item
 * @property int $price
 * @property int $heal
 * @property int $bakery_item
 * @property Collection<HealingItemRequired> $requiredItems
 * @property Collection<HealingItem> $bakery
 * @mixin \Eloquent
 */
class HealingItem extends Model
{
    public $timestamps = false;

    public function requiredItems(): HasMany
    {
        return $this->hasMany(HealingItemRequired::class, 'item_id', 'item_id');
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class, 'id', 'item_id');
    }
}
