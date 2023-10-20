<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $username
 * @property string $item
 * @property int $amount
 * @property int $id
 * @mixin \Eloquent
 */
class Inventory extends Model
{
    public $timestamps = false;
    public $table = 'inventory';

    public $guarded = [];
}
