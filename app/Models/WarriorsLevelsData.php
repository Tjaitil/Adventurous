<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $skill_level
 * @property int $next_level
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsLevelsData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsLevelsData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsLevelsData query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsLevelsData whereNextLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WarriorsLevelsData whereSkillLevel($value)
 * @mixin \Eloquent
 */
class WarriorsLevelsData extends Model
{
    public $timestamps = false;
}
