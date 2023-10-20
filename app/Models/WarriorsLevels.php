<?php

namespace App\models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

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
