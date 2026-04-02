<?php

namespace App\Models;

use Database\Factories\WarriorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $username
 * @property int $warrior_amount
 * @property int $mission_id
 * @property string $mission_countdown
 *
 * @method static \Database\Factories\WarriorFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warrior newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warrior newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warrior query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warrior whereMissionCountdown($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warrior whereMissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warrior whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Warrior whereWarriorAmount($value)
 *
 * @mixin \Eloquent
 */
class Warrior extends Model
{
    /**
     * @use HasFactory<WarriorFactory>
     */
    use HasFactory;

    public $timestamps = false;

    /**
     * @deprecated Will be removed in the future
     */
    public int $warrior_amount;

    protected $table = 'warrior';
}
