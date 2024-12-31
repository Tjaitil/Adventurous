<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\SmithyItemRequired
 *
 * @property int $item_id
 * @property string|null $required_item
 * @property int $amount
 * @property-read \App\Models\SmithyItem $smithyItem
 *
 * @method static \Illuminate\Database\Eloquent\Builder|SmithyItemRequired newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SmithyItemRequired newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SmithyItemRequired query()
 * @method static \Illuminate\Database\Eloquent\Builder|SmithyItemRequired whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmithyItemRequired whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmithyItemRequired whereRequiredItem($value)
 *
 * @mixin \Eloquent
 */
class SmithyItemRequired extends Model
{
    public $timestamps = false;

    protected $table = 'smithy_items_required';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<SmithyItem, $this>
     */
    public function smithyItem(): BelongsTo
    {
        return $this->belongsTo(SmithyItem::class, 'item_id');
    }
}
