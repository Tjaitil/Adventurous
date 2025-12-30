<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property string $training_type
 * @property int $time
 * @property int $experience
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingTypeData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingTypeData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingTypeData query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingTypeData whereExperience($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingTypeData whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingTypeData whereTrainingType($value)
 * @mixin \Eloquent
 */
class TrainingTypeData extends Model
{
    public $timestamps = false;
}
