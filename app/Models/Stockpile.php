<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $username
 * @property string $item
 * @property int $amount
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stockpile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stockpile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stockpile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stockpile whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stockpile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stockpile whereItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Stockpile whereUsername($value)
 * @mixin \Eloquent
 */
class Stockpile extends Model
{
    public $timestamps = false;

    public $table = 'stockpile';

    public $guarded = [];
}
