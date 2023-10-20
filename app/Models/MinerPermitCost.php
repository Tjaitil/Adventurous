<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $location
 * @property int $permit_cost
 * @property int $permit_amount
 * 
 * @mixin \Eloquent
 */
class MinerPermitCost extends Model
{
    public $timestamps = false;
}
