<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $store
 * @property float $discount
 * @property string $profiency
 * @property int $id
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
