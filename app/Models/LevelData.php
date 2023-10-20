<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * @property int $level
 * @property string $next_level
 * @property int max_farm_workers
 * @property int max_min_workers
 * @property int max_warriors
 * @property int max_efficiency_level
 * @mixin \Eloquent
 */
class LevelData extends Model
{
    public $timestamps = false;
}
