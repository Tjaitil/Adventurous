<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Mineral
 *
 * @property string $mineral_type
 * @property string $mineral_ore
 * @property int $miner_level
 * @property int $experience
 * @property int $time
 * @property int $min_per_period
 * @property int $max_per_period
 * @property int $permit_cost
 * @property string $location
 * @property int $id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mineral newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mineral newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mineral query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mineral whereExperience($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mineral whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mineral whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mineral whereMaxPerPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mineral whereMinPerPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mineral whereMinerLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mineral whereMineralOre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mineral whereMineralType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mineral wherePermitCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mineral whereTime($value)
 * @mixin \Eloquent
 */
class Mineral extends Model
{
    public $timestamps = false;
}
