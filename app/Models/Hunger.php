<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Hunger
 *
 * @property int $id
 * @property int $current
 * @property int|null $user_id
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Hunger newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Hunger newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Hunger query()
 * @method static \Illuminate\Database\Eloquent\Builder|Hunger whereCurrent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hunger whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hunger whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Hunger extends Model
{
    protected $table = 'hunger';

    protected $guarded = [];

    public $timestamps = false;
}
