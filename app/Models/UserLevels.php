<?php

namespace App\Models;

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
 * @property-read int $farmer_next_level_xp
 * @property-read int $miner_next_level_xp
 * @property-read int $trader_next_level_xp
 * @property-read int $warrior_next_level_xp
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LevelData> $nextLevel
 * @property-read int|null $next_level_count
 *
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
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevels whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevels whereWarriorLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLevels whereWarriorXp($value)
 *
 * @mixin \Eloquent
 */
class UserLevels extends Model
{
    protected $appends = [
        'farmer_next_level_xp',
        'miner_next_level_xp',
        'trader_next_level_xp',
        'warrior_next_level_xp',
    ];

    public $timestamps = false;

    /**
     * @return HasMany<LevelData>
     */
    public function nextLevel(): HasMany
    {
        return $this->hasMany(LevelData::class);
    }

    /**
     * Get experience needed for next farmer level
     *
     * @return int
     */
    public function getFarmerNextLevelXpAttribute()
    {
        return LevelData::where('level', $this->farmer_level)->value('next_level');
    }

    /**
     * Get experience needed for next miner level
     *
     * @return int
     */
    public function getMinerNextLevelXpAttribute()
    {
        return LevelData::where('level', $this->miner_level)->value('next_level');
    }

    /**
     * Get experience needed for next trader level
     *
     * @return int
     */
    public function getTraderNextLevelXpAttribute()
    {
        return LevelData::where('level', $this->trader_level)->value('next_level');
    }

    /**
     * Get experience needed for next warrior level
     *
     * @return int
     */
    public function getWarriorNextLevelXpAttribute()
    {
        return LevelData::where('level', $this->warrior_level)->value('next_level');
    }
}
