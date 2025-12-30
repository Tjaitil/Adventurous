<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
 * @method static \Database\Factories\MinerWorkforceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MinerWorkforce newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MinerWorkforce newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MinerWorkforce query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MinerWorkforce whereAvailWorkforce($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MinerWorkforce whereEfficiencyLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MinerWorkforce whereGolbak($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MinerWorkforce whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MinerWorkforce whereMineralQuantLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MinerWorkforce whereSnerpiir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MinerWorkforce whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MinerWorkforce whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MinerWorkforce whereWorkforceTotal($value)
 * @mixin \Eloquent
 */
class MinerWorkforce extends Model
{
    /**
     * @use HasFactory<\Database\Factories\MinerWorkforceFactory>
     */
    use HasFactory;

    public $timestamps = false;

    public $table = 'miner_workforce';

    public $guarded = [];

    /**
     * @return HasMany<\App\Models\Miner, $this>
     */
    public function miner(): HasMany
    {
        return $this->hasMany(Miner::class, 'username', 'username');
    }
}
