<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Hunger
 *
 * @property int $id
 * @property int $current
 * @property int $user_id
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\HungerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hunger newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hunger newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hunger query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hunger whereCurrent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hunger whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hunger whereUserId($value)
 * @mixin \Eloquent
 */
class Hunger extends Model
{
    /**
     * @use HasFactory<\Database\Factories\HungerFactory>
     */
    use HasFactory;

    protected $table = 'hunger';

    protected $guarded = [];

    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
