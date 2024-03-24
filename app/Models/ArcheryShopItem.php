<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\ArcheryShopItem
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ArcheryShopItemsRequired> $requiredItems
 * @property-read int|null $required_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SkillRequirement> $skillRequirements
 * @property-read int|null $skill_requirements_count
 * @method static \Illuminate\Database\Eloquent\Builder|ArcheryShopItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArcheryShopItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArcheryShopItem query()
 * @mixin \Eloquent
 */
class ArcheryShopItem extends Model
{
    protected $table = 'archery_shop_items';

    public $timestamps = false;
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<ArcheryShopItemsRequired>
     */
    public function requiredItems(): HasMany
    {
        return $this->hasMany(ArcheryShopItemsRequired::class, 'item_id', 'item_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<SkillRequirement>
     */
    public function skillRequirements(): HasMany
    {
        return $this->hasMany(SkillRequirement::class, 'item', 'item');
    }
}
