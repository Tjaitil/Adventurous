<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\UserLevels
 *
 * @property int $id
 * @property string $username
 * @property string $adventurer_respect
 * @property int $farmer_level
 * @property int $farmer_xp
 * @property int $miner_level
 * @property int $miner_xp
 * @property int $trader_level
 * @property int $trader_xp
 * @property int $warrior_level
 * @property int $warrior_xp
 * @property int|null $user_id
 * @property-read int $farmer_next_level_xp
 * @property-read int $miner_next_level_xp
 * @property-read int $trader_next_level_xp
 * @property-read int $warrior_next_level_xp
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LevelData> $nextLevel
 * @property-read int|null $next_level_count
 *
 * @method static \Database\Factories\UserLevelsFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevels newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevels newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevels query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevels whereAdventurerRespect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevels whereFarmerLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevels whereFarmerXp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevels whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevels whereMinerLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevels whereMinerXp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevels whereTraderLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevels whereTraderXp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevels whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevels whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevels whereWarriorLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevels whereWarriorXp($value)
 *
 * @mixin \Eloquent
 */
class UserLevels extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * @return HasMany<LevelData>
     */
    public function nextLevel(): HasMany
    {
        return $this->hasMany(LevelData::class);
    }
}
