<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $username
 * @property string $item
 * @property int $amount
 * @property int $id
 * @mixin \Eloquent
 */
class Stockpile extends Model
{
    public $timestamps = false;

    public $table = 'stockpile';

    public $guarded = [];
}
