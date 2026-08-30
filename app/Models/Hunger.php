<?php

namespace App\Models;

use App\Events\HungerUpdated;
use Database\Factories\HungerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Hunger
 *
 * @property int $id
 * @property int $current
 * @property int $user_id
 * @property-read User $user
 *
 * @method static \Database\Factories\HungerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hunger newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hunger newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hunger query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hunger whereCurrent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hunger whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Hunger whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Hunger extends Model
{
    /**
     * @use HasFactory<HungerFactory>
     */
    use HasFactory;

    protected $table = 'hunger';

    protected $guarded = [];

    public $timestamps = false;

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function boot()
    {
        parent::boot();

        static::saved(function ($hunger) {
            event(new HungerUpdated($hunger));
        });
    }
}
