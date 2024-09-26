<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $username
 * @property string $item
 * @property int $amount
 * @property-read \App\Models\Item|null $itemData
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory query()
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory whereItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory whereUsername($value)
 *
 * @mixin \Eloquent
 */
class Inventory extends Model
{
    public $timestamps = false;

    public $table = 'inventory';

    public $guarded = [];
}
