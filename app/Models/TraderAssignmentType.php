<?php

namespace App\Models;

use App\Enums\TraderAssignmentTypeName;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property TraderAssignmentTypeName $type
 * @property float $xp_per_cargo
 * @property int $item_reward_amount
 * @property int $xp_finished
 * @property float $diplomacy_percentage
 * @property int $currency_reward_amount
 * @property int $required_level
 * @property int $xp_started
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TraderAssignmentType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TraderAssignmentType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TraderAssignmentType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TraderAssignmentType whereCurrencyRewardAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TraderAssignmentType whereDiplomacyPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TraderAssignmentType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TraderAssignmentType whereItemRewardAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TraderAssignmentType whereRequiredLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TraderAssignmentType whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TraderAssignmentType whereXpFinished($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TraderAssignmentType whereXpPerCargo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TraderAssignmentType whereXpStarted($value)
 *
 * @mixin \Eloquent
 */
class TraderAssignmentType extends Model
{
    public $timestamps = false;

    public static $FAVOR_TYPE = 'favor';

    public function casts(): array
    {
        return [
            'diplomacy_percentage' => 'float',
            'type' => TraderAssignmentTypeName::class,
        ];
    }
}
