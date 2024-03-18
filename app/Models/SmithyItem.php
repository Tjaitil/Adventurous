<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\SmithyItem
 *
 * @property int $item_id
 * @property string $item
 * @property int $store_value
 * @property string $mineral
 * @property int $item_multiplier
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SmithyItemRequired> $requiredItems
 * @property-read int|null $required_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SkillRequirement> $skillRequirements
 * @property-read int|null $skill_requirements_count
 * @method static \Illuminate\Database\Eloquent\Builder|SmithyItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SmithyItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SmithyItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|SmithyItem whereItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmithyItem whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmithyItem whereItemMultiplier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmithyItem whereMineral($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmithyItem whereStoreValue($value)
 * @mixin \Eloquent
 */
class SmithyItem extends Model
{
    public $timestamps = false;

    protected $table = 'smithy_items';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<SmithyItemRequired>
     */
    public function requiredItems(): HasMany
    {
        return $this->hasMany(SmithyItemRequired::class, 'item_id', 'item_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<SkillRequirement>
     */
    public function skillRequirements(): HasMany
    {
        return $this->hasMany(SkillRequirement::class, 'item', 'item');
    }
}
