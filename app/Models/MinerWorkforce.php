<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;


/**
 * @property int $id
 * @property string $username
 * @property int $workforce_total
 * @property int $avail_workforce
 * @property int $golbak
 * @property int $snerpiir
 * @property int $efficiency_level
 * @property Collection<Miner> $miner
 */
class MinerWorkforce extends Model
{
    public $timestamps = false;

    public $table = 'miner_workforce';

    public $guarded = [];

    public function miner()
    {
        return $this->hasMany(Miner::class, 'username', 'username');
    }
}
