<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Miner
 *
 * @property string $username
 * @property string|null $mineral_ore
 * @property \Carbon\CarbonInterface $mining_finishes_at
 * @property int $permits
 * @property string $location
 * @property int $id
 * @property int $user_id
 * @property-read \App\Models\MinerWorkforce $workforce
 * @method static \Database\Factories\MinerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Miner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Miner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Miner query()
 * @method static \Illuminate\Database\Eloquent\Builder|Miner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Miner whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Miner whereMineralOre($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Miner whereMiningFinishesAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Miner wherePermits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Miner whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Miner whereUsername($value)
 * @mixin \Eloquent
 */
class Miner extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $table = 'miner';

    protected $guarded = [];

    protected $casts = ['mining_finishes_at' => 'datetime'];

    public function workforce(): BelongsTo
    {
        return $this->belongsTo(MinerWorkforce::class, 'user_id', 'user_id');
    }
}
