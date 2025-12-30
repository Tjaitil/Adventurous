<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $item_id
 * @property string $item
 * @property int $mineral_required
 * @property int $wood_required
 * @property int $level
 * @property int $attack
 * @property int $defence
 * @property int $price
 * @property string $type
 * @property string $warrior_type
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArmorItems newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArmorItems newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArmorItems query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArmorItems whereAttack($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArmorItems whereDefence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArmorItems whereItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArmorItems whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArmorItems whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArmorItems whereMineralRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArmorItems wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArmorItems whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArmorItems whereWarriorType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArmorItems whereWoodRequired($value)
 * @mixin \Eloquent
 */
class ArmorItems extends Model
{
    public $timestamps = false;

    protected $table = 'armory_items_data';
}
