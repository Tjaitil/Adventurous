<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $username
 * @property int $warrior_id
 * @property int $stamina_level
 * @property int|null $stamina_xp
 * @property int $technique_level
 * @property int|null $technique_xp
 * @property int $precision_level
 * @property int|null $precision_xp
 * @property int $strength_level
 * @property int|null $strength_xp
 * @property-read \Attribute $precision_next_level_xp
 * @property-read \Attribute $stamina_next_level_xp
 * @property-read \Attribute $strength_next_level_xp
 * @property-read \Attribute $technique_next_level_xp
 * @property-read \App\Models\Warrior|null $warrior
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsLevels newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsLevels newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsLevels query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsLevels whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsLevels wherePrecisionLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsLevels wherePrecisionXp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsLevels whereStaminaLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsLevels whereStaminaXp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsLevels whereStrengthLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsLevels whereStrengthXp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsLevels whereTechniqueLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsLevels whereTechniqueXp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsLevels whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsLevels whereWarriorId($value)
 * @mixin \Eloquent
 */
class WarriorsLevels extends Model
{
    protected $appends = [
        'precision_next_level_xp',
        'technique_next_level_xp',
        'strength_next_level_xp',
        'stamina_next_level_xp'
    ];

    public $timestamps = false;

    public function warrior()
    {
        return $this->hasOne(Warrior::class, 'id', 'id');
    }

    /**
     * Get experience needed for next precision level
     *
     * @return Attribute
     */
    public function getPrecisionNextLevelXpAttribute()
    {
        return WarriorsLevelsData::where('skill_level', $this->precision_level)->value('next_level');
    }

    /**
     * Get experience needed for next technique level
     *
     * @return Attribute
     */
    public function getTechniqueNextLevelXpAttribute()
    {
        return WarriorsLevelsData::where('skill_level', $this->technique_level)->value('next_level');
    }

    /**
     * Get experience needed for next strength level
     *
     * @return Attribute
     */
    public function getStrengthNextLevelXpAttribute()
    {
        return WarriorsLevelsData::where('skill_level', $this->strength_level)->value('next_level');
    }

    /**
     * Get experience needed for next stamina level
     *
     * @return Attribute
     */
    public function getStaminaNextLevelXpAttribute()
    {
        return WarriorsLevelsData::where('skill_level', $this->stamina_level)->value('next_level');
    }
}
