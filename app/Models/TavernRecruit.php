<?php

namespace App\Models;

use Database\Factories\TavernRecruitFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property string $location
 * @property int $price
 * @property int $level
 * @property int $type_id
 * @property int $user_id
 * @property \Carbon\CarbonInterface|null $created_at
 * @property \Carbon\CarbonInterface|null $updated_at
 * @property \Carbon\CarbonInterface|null $recruited_at
 * @property-read \App\Models\TavernRecruitType $recruitType
 * @method static \Database\Factories\TavernRecruitFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TavernRecruit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TavernRecruit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TavernRecruit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TavernRecruit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TavernRecruit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TavernRecruit whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TavernRecruit whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TavernRecruit wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TavernRecruit whereRecruitedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TavernRecruit whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TavernRecruit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TavernRecruit whereUserId($value)
 * @mixin \Eloquent
 */
class TavernRecruit extends Model
{
    /**
     * @use HasFactory<TavernRecruitFactory>
     */
    use HasFactory;

    protected static string $factory = TavernRecruitFactory::class;

    protected $table = 'tavern_recruits';

    protected function casts()
    {
        return [
            'recruited_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<TavernRecruitType, $this>
     */
    public function recruitType(): BelongsTo
    {
        return $this->belongsTo(TavernRecruitType::class, 'type_id', 'id');
    }
}
