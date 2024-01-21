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
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LevelData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LevelData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LevelData query()
 * @method static \Illuminate\Database\Eloquent\Builder|LevelData whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LevelData whereMaxEfficiencyLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LevelData whereMaxFarmWorkers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LevelData whereMaxMineWorkers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LevelData whereMaxWarriors($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LevelData whereNextLevel($value)
 *
 * @mixin \Eloquent
 */
class LevelData extends Model
{
    public $timestamps = false;
}
