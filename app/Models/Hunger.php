<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $current
 * @property int $user_id
 * @mixin \Eloquent
 */
class Hunger extends Model
{
    protected $table = "hunger";

    protected $guarded = [];

    public $timestamps = false;
}
