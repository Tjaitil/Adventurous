<?php

namespace App\Models;

use App\Enums\ArmoryParts;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $item_id
 * @property string $item
 * @property int $mineral_required
 * @property int $wood_required
 * @property int $level
 * @property int $attack
 * @property int $defence
 * @property int $price
 * @property ArmoryParts $type
 * @property string $warrior_type
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ArmoryItemsData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArmoryItemsData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArmoryItemsData query()
 * @method static \Illuminate\Database\Eloquent\Builder|ArmoryItemsData whereAttack($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArmoryItemsData whereDefence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArmoryItemsData whereItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArmoryItemsData whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArmoryItemsData whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArmoryItemsData whereMineralRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArmoryItemsData wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArmoryItemsData whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArmoryItemsData whereWarriorType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArmoryItemsData whereWoodRequired($value)
 *
 * @mixin \Eloquent
 */
class ArmoryItemsData extends Model
{
    public $timestamps = false;

    /**
     * @var array<string, string>
     */
    public $casts = [
        'type' => ArmoryParts::class,
    ];

    public static function getMineralFromItem(ArmoryItemsData $item): ?string
    {

        return match (true) {
            strpos($item->item, 'iron') !== false => 'iron',
            strpos($item->item, 'steel') !== false => 'steel',
            strpos($item->item, 'yeqdon') !== false => 'yeqdon',
            strpos($item->item, 'adron') !== false => 'adron',
            strpos($item->item, 'frajrite') !== false => 'frajrite',
            strpos($item->item, 'wujkin') !== false => 'wujkin',
            default => null,
        };
    }
}
