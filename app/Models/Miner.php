<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $username
 * @property string $mineral_type
 * @property Carbon $mining_finishes_at
 * @property int $permits
 * @property string $location
 * @property MinerWorkforce $workforce
 * @mixin \Eloquent
 */
class Miner extends Model
{
    public $timestamps = false;

    public $table = 'miner';

    protected $guarded = [];

    protected $dates = ['mining_finishes_at'];

    public function workforce()
    {
        return $this->belongsTo(MinerWorkforce::class, 'username', 'username');
    }
}
