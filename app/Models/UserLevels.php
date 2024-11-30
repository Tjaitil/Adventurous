<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
 * @property-read mixed $farmer_next_level_xp
 * @property-read mixed $miner_next_level_xp
 * @property-read mixed $trader_next_level_xp
 * @property-read mixed $warrior_next_level_xp
 * @method static \Database\Factories\UserLevelsFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLevels newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLevels newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLevels query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLevels whereAdventurerRespect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLevels whereFarmerLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLevels whereFarmerXp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLevels whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLevels whereMinerLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLevels whereMinerXp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLevels whereTraderLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLevels whereTraderXp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLevels whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLevels whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLevels whereWarriorLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserLevels whereWarriorXp($value)
 * @mixin \Eloquent
 */
class UserLevels extends Model
{
    /** @use HasFactory<\Database\Factories\UserLevelsFactory> */
    use HasFactory;

    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $appends = [
        'farmer_next_level_xp',
        'trader_next_level_xp',
        'miner_next_level_xp',
        'warrior_next_level_xp',
    ];

    /**
     * @return Attribute<int, never>
     */
    protected function farmerNextLevelXp(): Attribute
    {
        return Attribute::make(
            get: fn () => LevelData::getNextLevelXp($this->farmer_level),
        );
    }

    /**
     * @return Attribute<int|null, never>
     */
    protected function minerNextLevelXp(): Attribute
    {
        return Attribute::make(
            get: fn () => LevelData::getNextLevelXp($this->miner_level),
        );
    }

    /**
     * @return Attribute<int|null, never>
     */
    protected function traderNextLevelXp(): Attribute
    {
        return Attribute::make(
            get: fn () => LevelData::getNextLevelXp($this->trader_level),

        );
    }

    /**
     * @return Attribute<int|null, never>
     */
    protected function warriorNextLevelXp(): Attribute
    {
        return Attribute::make(
            get: fn () => LevelData::getNextLevelXp($this->warrior_level),
        );
    }
}
