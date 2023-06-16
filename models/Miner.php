<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Miner extends Model
{
    public $timestamps = false;

    public $table = 'miner';

    protected $guarded = [];

    protected $dates = ['mining_countdown', 'mining_started'];
}
