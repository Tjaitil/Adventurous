<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\HealingItemRequired
 *
 * @property int $item_id
 * @property string $required_item
 * @property int $amount
 * @property int $id
 * @property-read \App\Models\HealingItem $healingItem
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealingItemRequired newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealingItemRequired newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealingItemRequired query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealingItemRequired whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealingItemRequired whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealingItemRequired whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealingItemRequired whereRequiredItem($value)
 * @mixin \Eloquent
 */
class HealingItemRequired extends Model
{
    public $timestamps = false;

    public $table = 'healing_items_required';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<HealingItem, $this>
     */
    public function healingItem(): BelongsTo
    {
        return $this->belongsTo(HealingItem::class);
    }
}
