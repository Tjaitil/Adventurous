<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    public $timestamps = false;
    public $table = 'inventory';

    public $fillable = [
        'item',
        'amount',
        'username'
    ];
}
