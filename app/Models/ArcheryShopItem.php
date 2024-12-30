<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\ArcheryShopItem
 *
 * @property int $id
 * @property int $item_id
 * @property string $item
 * @property int|null $item_multiplier
 * @property int $store_value
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ArcheryShopItemsRequired> $requiredItems
 * @property-read int|null $required_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SkillRequirement> $skillRequirements
 * @property-read int|null $skill_requirements_count
 * @method static \Illuminate\Database\Eloquent\Builder|ArcheryShopItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArcheryShopItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArcheryShopItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|ArcheryShopItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArcheryShopItem whereItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArcheryShopItem whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArcheryShopItem whereItemMultiplier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArcheryShopItem whereStoreValue($value)
 * @mixin \Eloquent
 */
class ArcheryShopItem extends Model
{
    protected $table = 'archery_shop_items';

    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<ArcheryShopItemsRequired, $this>
     */
    public function requiredItems(): HasMany
    {
        return $this->hasMany(ArcheryShopItemsRequired::class, 'item_id', 'item_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<SkillRequirement, $this>
     */
    public function skillRequirements(): HasMany
    {
        return $this->hasMany(SkillRequirement::class, 'item', 'item');
    }
}
