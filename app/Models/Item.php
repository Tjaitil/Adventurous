<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $item_id
 * @property string $name
 * @property int $store_value
 * @property int $in_game
 * @property int $towhar_rate
 * @property int $golbak_rate
 * @property int $snerpiir_rate
 * @property int $cruendo_rate
 * @property int $pvitul_rate
 * @property int $khanz_rate
 * @property int $ter_rate
 * @property int $krasnur_rate
 * @property int $hirtam_rate
 * @property int $fansal_plains_rate
 * @property int $tasnobil_rate
 * @property string $trader_assignment_type
 * @property int $adventure_requirement
 * @property string $adventure_requirement_difficulty
 * @property string $adventure_requirement_role
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereAdventureRequirement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereAdventureRequirementDifficulty($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereAdventureRequirementRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereCruendoRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereFansalPlainsRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereGolbakRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereHirtamRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereInGame($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereKhanzRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereKrasnurRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item wherePvitulRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereSnerpiirRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereStoreValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereTasnobilRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereTerRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereTowharRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereTraderAssignmentType($value)
 * @mixin \Eloquent
 */
class Item extends Model
{
    public $timestamps = false;
}
