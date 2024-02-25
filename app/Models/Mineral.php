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
 * @method static \Illuminate\Database\Eloquent\Builder|Mineral newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mineral newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mineral query()
 * @method static \Illuminate\Database\Eloquent\Builder|Mineral whereExperience($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mineral whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mineral whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mineral whereMaxPerPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mineral whereMinPerPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mineral whereMinerLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mineral whereMineralOre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mineral whereMineralType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mineral wherePermitCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mineral whereTime($value)
 * @mixin \Eloquent
 */
class Mineral extends Model
{
    public $timestamps = false;
}
