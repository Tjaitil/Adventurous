<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $item_id
 * @property int $amount
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read \App\Models\Item $item
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stockpile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stockpile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stockpile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stockpile whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stockpile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stockpile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stockpile whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stockpile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stockpile whereUserId($value)
 * @mixin \Eloquent
 */
class Stockpile extends Model
{
    public $timestamps = false;

    public $table = 'stockpile';

    public $guarded = [];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Item, $this>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'item_id');
    }
}
