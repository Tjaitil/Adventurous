<?php

namespace App\Models;

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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warriors available()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warriors newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warriors newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warriors query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warriors whereArmyMission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warriors whereHealth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warriors whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warriors whereIsResting($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warriors whereIsTraining($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warriors whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warriors whereRestStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warriors whereTrainingCountdown($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warriors whereTrainingType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warriors whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warriors whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warriors whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warriors whereWarriorId($value)
 * @mixin \Eloquent
 */
class Warriors extends Soldier {}
