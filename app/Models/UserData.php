<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $username
 * @property string $location
 * @property string $map_location
 * @property string $game_id
 * @property int $session_id
 * @property string $destination
 * @property string $arrive_time
 * @property string $horse
 * @property string $artefact
 * @property int $hunger
 * @property int $hunger_date
 * @property string $frajrite_items
 * @property string $wujkin_items
 * @property string $stockpile_max_amount
 * @mixin \Eloquent
 */
class UserData extends Model
{
    public $timestamps = false;

    public function __construct()
    {
        parent::__construct();
    }
}
