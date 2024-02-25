<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\MinerWorkforce
 *
 * @property string $username
 * @property int $workforce_total
 * @property int $avail_workforce
 * @property int $golbak
 * @property int $snerpiir
 * @property int $mineral_quant_level
 * @property int $id
 * @property int|null $efficiency_level
 * @property int $user_id
 * @property-read Collection<int, \App\Models\Miner> $miner
 * @property-read int|null $miner_count
 * @method static \Illuminate\Database\Eloquent\Builder|MinerWorkforce newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MinerWorkforce newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MinerWorkforce query()
 * @method static \Illuminate\Database\Eloquent\Builder|MinerWorkforce whereAvailWorkforce($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MinerWorkforce whereEfficiencyLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MinerWorkforce whereGolbak($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MinerWorkforce whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MinerWorkforce whereMineralQuantLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MinerWorkforce whereSnerpiir($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MinerWorkforce whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MinerWorkforce whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MinerWorkforce whereWorkforceTotal($value)
 * @mixin \Eloquent
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
