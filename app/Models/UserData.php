<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserData
 *
 * @property int $id
 * @property string $username
 * @property string $location
 * @property string $map_location
 * @property string $game_id
 * @property int $session_id
 * @property string $destination
 * @property \Carbon\Carbon $arrive_time
 * @property string $profiency
 * @property string $horse
 * @property string $artefact
 * @property int $hunger
 * @property string $hunger_date
 * @property bool $frajrite_items
 * @property bool $wujkin_items
 * @property int|null $stockpile_max_amount
 * @method static \Database\Factories\UserDataFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserData query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserData whereArriveTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserData whereArtefact($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserData whereDestination($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserData whereFrajriteItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserData whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserData whereHorse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserData whereHunger($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserData whereHungerDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserData whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserData whereMapLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserData whereProfiency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserData whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserData whereStockpileMaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserData whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserData whereWujkinItems($value)
 * @mixin \Eloquent
 */
class UserData extends Model
{
    /**
     * @use HasFactory<\Database\Factories\UserDataFactory>
     */
    use HasFactory;

    protected $table = 'user_data';

    public $timestamps = false;

    public $guarded = [];

    /**
     * @var array<string, string>
     */
    protected $casts =
        [
            'frajrite_items' => 'boolean',
            'wujkin_items' => 'boolean',
            'arrive_time' => 'datetime',
        ];

    public function isWujkinItemUnlocked(): bool
    {
        return $this->wujkin_items;
    }

    public function isFrajriteItemUnlocked(): bool
    {
        return $this->frajrite_items;
    }
}
