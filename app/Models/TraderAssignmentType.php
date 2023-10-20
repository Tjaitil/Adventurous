<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $type
 * @property int $xp_per_cargo
 * @property int $xp_finished
 * @property int $xp_started
 * @property int $item_reward_amount
 * @property int $currency_reward_amount
 * @property float $diplomacy_percentage
 * @property int $required_level
 * @mixin \Eloquent
 */
class TraderAssignmentType extends Model
{
    public $timestamps = false;
    public static $FAVOR_TYPE = "favor";
}
