<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Farmer
 * @property int $id
 * @property string $username
 * @property Carbon $crop_finishes_at
 * @property int $crop_quant
 * @property string $location
 * @property FarmerWorkforce $workforce
 * @mixin \Eloquent
 */
class Farmer extends Model
{
    public $timestamps = false;

    public $table = 'farmer';

    protected $guarded = [];

    protected $dates = ['crop_finishes_at'];


    public function workforce(): HasOne
    {
        return $this->hasOne(FarmerWorkforce::class, 'username', 'username');
    }
}
