<?php

namespace App\Models;

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
 * @property \Carbon\CarbonInterface $arrive_time
 * @property string $profiency
 * @property string $horse
 * @property string $artefact
 * @property int $hunger
 * @property string $hunger_date
 * @property bool $frajrite_items
 * @property bool $wujkin_items
 * @property int|null $stockpile_max_amount
 * @method static \Illuminate\Database\Eloquent\Builder|UserData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserData query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereArriveTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereArtefact($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereDestination($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereFrajriteItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereHorse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereHunger($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereHungerDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereMapLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereProfiency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereStockpileMaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereWujkinItems($value)
 * @mixin \Eloquent
 */
class UserData extends Model
{
    public $timestamps = false;

    /**
     * @var array
     */
    protected $casts =
    [
        'frajrite_items' => 'boolean',
        'wujkin_items' => 'boolean',
        'arrive_time' => 'datetime',
    ];

    public function __construct()
    {
        parent::__construct();
    }
}
