<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $location
 * @property int $permit_cost
 * @property int $permit_amount
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MinerPermitCost newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MinerPermitCost newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MinerPermitCost query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MinerPermitCost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MinerPermitCost whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MinerPermitCost wherePermitAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MinerPermitCost wherePermitCost($value)
 * @mixin \Eloquent
 */
class MinerPermitCost extends Model
{
    public $timestamps = false;

    protected $table = 'miner_permit_cost';
}
