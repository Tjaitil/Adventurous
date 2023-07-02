<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Stockpile extends Model
{
    public $timestamps = false;

    public $table = 'stockpile';

    public $fillable = [
        'username',
        'item',
        'amount',
    ];
}
