<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $wheel
 * @property string $wood
 * @property int $value
 * @property int $capasity
 * @property int $towhar
 * @property int $golbak
 * @property int $mineral_amount
 * @property int $wood_amount
 * \Eloquent
 */
class TravelBureauCart extends Model
{
    protected $guarded = [];

    public $table = 'travelbureau_carts';
}
