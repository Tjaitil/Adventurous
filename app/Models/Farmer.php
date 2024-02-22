<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\Farmer
 *
 * @property string $username
 * @property int $fields
 * @property string|null $crop_type
 * @property int $crop_quant
 * @property \Carbon\CarbonInterface|null $crop_finishes_at
 * @property string $location
 * @property int $id
 * @property int $user_id
 * @property-read \App\Models\FarmerWorkforce|null $workforce
 * @method static \Illuminate\Database\Eloquent\Builder|Farmer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Farmer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Farmer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Farmer whereCropFinishesAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Farmer whereCropQuant($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Farmer whereCropType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Farmer whereFields($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Farmer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Farmer whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Farmer whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Farmer whereUsername($value)
 * @mixin \Eloquent
 */
class Farmer extends Model
{
    public $timestamps = false;

    public $table = 'farmer';

    protected $guarded = [];

    protected $casts = [
        'crop_finishes_at' => 'datetime',
    ];

    public function workforce(): HasOne
    {
        return $this->hasOne(FarmerWorkforce::class, 'user_id', 'user_id');
    }
}
