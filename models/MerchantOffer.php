<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;


/**
 * @property int id
 * @property string location
 * @property string item
 * @property int store_value
 * @property int sell_value
 * @property int amount
 * @property Carbon date_inserted
 * @package App\models
 */
class MerchantOffer extends Model
{
    public $timestamps = false;

    protected $guarded = [];
    protected $dates = ['date_inserted'];
}
