<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property string $store
 * @property float $discount
 * @property string $profiency
 * @property int $id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreDiscount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreDiscount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreDiscount query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreDiscount whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreDiscount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreDiscount whereProfiency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoreDiscount whereStore($value)
 * @mixin \Eloquent
 */
class StoreDiscount extends Model
{
    public $timestamps = false;


    /**
     * 
     * @return int
     */
    public function toPercentage()
    {
        $percentage = $this->discount * 100;
        if ($percentage === 100) {
            return 0;
        } else {
            return $percentage;
        }
    }
}
