<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\TravelBureauCart
 *
 * @property string $name
 * @property string $wheel
 * @property string $wood
 * @property int $store_value
 * @property int $capasity
 * @property int $towhar
 * @property int $golbak
 * @property int $mineral_amount
 * @property int $wood_amount
 * @property int|null $item_id
 * @property int $id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TravelBureauCartRequiredItem> $requiredItems
 * @property-read int|null $required_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SkillRequirement> $skillRequirements
 * @property-read int|null $skill_requirements_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TravelBureauCart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TravelBureauCart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TravelBureauCart query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TravelBureauCart whereCapasity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TravelBureauCart whereGolbak($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TravelBureauCart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TravelBureauCart whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TravelBureauCart whereMineralAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TravelBureauCart whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TravelBureauCart whereStoreValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TravelBureauCart whereTowhar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TravelBureauCart whereWheel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TravelBureauCart whereWood($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TravelBureauCart whereWoodAmount($value)
 * @mixin \Eloquent
 */
class TravelBureauCart extends Model
{
    protected $guarded = [];

    public $table = 'travelbureau_carts';

    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<TravelBureauCartRequiredItem, $this>
     */
    public function requiredItems(): HasMany
    {
        return $this->hasMany(TravelBureauCartRequiredItem::class, 'item_id', 'item_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\SkillRequirement, $this>
     */
    public function skillRequirements(): HasMany
    {
        return $this->hasMany(SkillRequirement::class, 'item', 'name');
    }
}
