<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property string $username
 * @property int $workforce_total
 * @property int $avail_workforce
 * @property int $towhar
 * @property int $krasnur
 * @property int $efficiency_level
 * @property int $id
 * @property int $user_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Farmer> $farmer
 * @property-read int|null $farmer_count
 * @method static \Database\Factories\FarmerWorkforceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FarmerWorkforce newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FarmerWorkforce newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FarmerWorkforce query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FarmerWorkforce whereAvailWorkforce($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FarmerWorkforce whereEfficiencyLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FarmerWorkforce whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FarmerWorkforce whereKrasnur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FarmerWorkforce whereTowhar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FarmerWorkforce whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FarmerWorkforce whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FarmerWorkforce whereWorkforceTotal($value)
 * @mixin \Eloquent
 */
class FarmerWorkforce extends Model
{
    /**
     * @use HasFactory<\Database\Factories\FarmerWorkforceFactory>
     */
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    public $table = 'farmer_workforce';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Farmer, $this>
     */
    public function farmer(): HasMany
    {
        return $this->hasMany(Farmer::class, 'username', 'username');
    }
}
