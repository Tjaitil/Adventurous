<?php

namespace App\Models;

use App\Enums\TavernRecruitTypes;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property TavernRecruitTypes $name
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TavernRecruitType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TavernRecruitType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TavernRecruitType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TavernRecruitType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TavernRecruitType whereName($value)
 * @mixin \Eloquent
 */
class TavernRecruitType extends Model
{
    protected $table = 'tavern_recruit_types';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'name' => TavernRecruitTypes::class,
        ];
    }
}
