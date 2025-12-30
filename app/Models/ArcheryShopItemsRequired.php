<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\ArcheryShopItemsRequired
 *
 * @property int $item_id
 * @property string $required_item
 * @property int $amount
 * @property int $id
 * @property-read \App\Models\ArcheryShopItem $smithyItem
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArcheryShopItemsRequired newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArcheryShopItemsRequired newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArcheryShopItemsRequired query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArcheryShopItemsRequired whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArcheryShopItemsRequired whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArcheryShopItemsRequired whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArcheryShopItemsRequired whereRequiredItem($value)
 * @mixin \Eloquent
 */
class ArcheryShopItemsRequired extends Model
{
    protected $table = 'archery_shop_items_required';

    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<ArcheryShopItem, $this>
     */
    public function smithyItem(): BelongsTo
    {
        return $this->belongsTo(ArcheryShopItem::class, 'item_id');
    }
}
