<?php

namespace App\Models;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * 
 *
 * @property int $id
 * @property string $username
 * @property int $warrior_id
 * @property string $type
 * @property \Carbon\Carbon $training_countdown
 * @property bool|null $is_training
 * @property string|null $training_type
 * @property int $army_mission
 * @property int $health
 * @property string $location
 * @property bool $is_resting
 * @property \Carbon\Carbon $rest_start
 * @property int $user_id
 * @property-read \App\Models\SoldierArmory|null $armory
 * @property-read \App\Models\WarriorsLevels|null $levels
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Soldier available()
 * @method static \Database\Factories\SoldierFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Soldier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Soldier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Soldier query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Soldier whereArmyMission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Soldier whereHealth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Soldier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Soldier whereIsResting($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Soldier whereIsTraining($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Soldier whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Soldier whereRestStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Soldier whereTrainingCountdown($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Soldier whereTrainingType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Soldier whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Soldier whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Soldier whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Soldier whereWarriorId($value)
 * @mixin \Eloquent
 */
class Soldier extends Model
{
    /**
     * @use HasFactory<\Database\Factories\SoldierFactory>
     */
    use HasFactory;

    protected $table = 'warriors';

    protected $fillable = ['warrior_id', 'location'];

    public $timestamps = false;

    public $casts = [
        'training_countdown' => 'datetime',
        'rest_start' => 'datetime',
        'is_resting' => 'boolean',
        'is_training' => 'boolean',
    ];

    /**
     * @return HasOne<WarriorsLevels, $this>
     */
    public function levels(): HasOne
    {
        return $this->hasOne(WarriorsLevels::class, 'id', 'id');
    }

    /**
     * @return HasOne<User, $this>
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @return HasOne<SoldierArmory, $this>
     */
    public function armory(): HasOne
    {
        return $this->hasOne(SoldierArmory::class, 'id', 'id');
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query
            ->where('is_training', false)
            ->where('army_mission', 0)
            ->where('is_resting', false);
    }
}
