<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\HealingItem
 *
 * @property int $item_id
 * @property string $item
 * @property int $price
 * @property int $heal
 * @property int $bakery_item
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Inventory> $inventory
 * @property-read int|null $inventory_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HealingItemRequired> $requiredItems
 * @property-read int|null $required_items_count
 *
 * @method static Builder|HealingItem newModelQuery()
 * @method static Builder|HealingItem newQuery()
 * @method static Builder|HealingItem query()
 * @method static Builder|HealingItem whereBakeryItem($value)
 * @method static Builder|HealingItem whereHeal($value)
 * @method static Builder|HealingItem whereItem($value)
 * @method static Builder|HealingItem whereItemId($value)
 * @method static Builder|HealingItem wherePrice($value)
 *
 * @mixin \Eloquent
 */
class HealingItem extends Model
{
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<HealingItemRequired, $this>
     */
    public function requiredItems(): HasMany
    {
        return $this->hasMany(HealingItemRequired::class, 'item_id', 'item_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Inventory, $this>
     */
    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class, 'id', 'item_id');
    }
}
