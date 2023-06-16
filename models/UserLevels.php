<?php

namespace App\models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class UserLevels extends Model
{
    protected $appends = [
        'farmer_next_level_xp',
        'miner_next_level_xp',
        'trader_next_level_xp',
        'warrior_next_level_xp'
    ];

    public $timestamps = false;

    public function nextLevel()
    {
        return $this->hasMany(LevelData::class);
    }

    /**
     * Get experience needed for next farmer level
     *
     * @return Attribute
     */
    public function getFarmerNextLevelXpAttribute()
    {
        return LevelData::where('level', $this->farmer_level)->value('next_level');
    }

    /**
     * Get experience needed for next miner level
     *
     * @return Attribute
     */
    public function getMinerNextLevelXpAttribute()
    {
        return LevelData::where('level', $this->miner_level)->value('next_level');
    }

    /**
     * Get experience needed for next trader level
     *
     * @return Attribute
     */
    public function getTraderNextLevelXpAttribute()
    {
        return LevelData::where('level', $this->trader_level)->value('next_level');
    }

    /**
     * Get experience needed for next warrior level
     *
     * @return Attribute
     */
    public function getWarriorNextLevelXpAttribute()
    {
        return LevelData::where('level', $this->warrior_level)->value('next_level');
    }
}
