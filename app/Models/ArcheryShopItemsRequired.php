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
 * @property-read \App\Models\ArcheryShopItem|null $smithyItem
 * @method static \Illuminate\Database\Eloquent\Builder|ArcheryShopItemsRequired newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArcheryShopItemsRequired newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArcheryShopItemsRequired query()
 * @method static \Illuminate\Database\Eloquent\Builder|ArcheryShopItemsRequired whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArcheryShopItemsRequired whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArcheryShopItemsRequired whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArcheryShopItemsRequired whereRequiredItem($value)
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
