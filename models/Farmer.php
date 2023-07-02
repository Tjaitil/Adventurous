<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Farmer extends Model
{
    public $timestamps = false;

    public $table = 'farmer';

    protected $guarded = [];

    protected $dates = ['crop_countdown'];
}
