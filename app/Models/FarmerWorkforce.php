<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $username
 * @property int $workforce_total
 * @property int $avail_workforce
 * @property int $towhar
 * @property int $krasnur
 * @property int $efficiency_level
 * @property Collection<Farmer> $farmer
 * @mixin \Eloquent
 */
class FarmerWorkforce extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    public $table = 'farmer_workforce';

    public function farmer(): HasMany
    {
        return $this->hasMany(Farmer::class, 'username', 'username');
    }
}
