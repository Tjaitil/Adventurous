<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LevelData
 *
 * @property int $level
 * @property int|null $next_Level
 * @property int|null $max_farm_workers
 * @property int|null $max_mine_workers
 * @property int|null $max_warriors
 * @property int|null $max_efficiency_level
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LevelData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LevelData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LevelData query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LevelData whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LevelData whereMaxEfficiencyLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LevelData whereMaxFarmWorkers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LevelData whereMaxMineWorkers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LevelData whereMaxWarriors($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LevelData whereNextLevel($value)
 * @mixin \Eloquent
 */
class LevelData extends Model
{
    public $timestamps = false;

    public static function getNextLevelXp(int $level): ?int
    {
        $nextLevel = self::where('level', $level)->first();
        if (! $nextLevel instanceof self) {
            return null;
        }

        return $nextLevel->next_Level;
    }
}
