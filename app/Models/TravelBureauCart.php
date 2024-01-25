<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\TravelBureauCart
 *
 * @property int $id
 * @property int $item_id
 * @property string $item
 * @property string $wheel
 * @property string $wood
 * @property int $value
 * @property int $capasity
 * @property int $towhar
 * @property int $golbak
 * @property int $mineral_amount
 * @property int $wood_amount
 * @property Collection<TravelBureauCartRequiredItem> $required_items
 * @property Collection<SkillRequirement> $skill_requirements
 * @property string $name
 * @property int $store_value
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TravelBureauCartRequiredItem> $requiredItems
 * @property-read int|null $required_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SkillRequirement> $skillRequirements
 * @property-read int|null $skill_requirements_count
 * @method static \Illuminate\Database\Eloquent\Builder|TravelBureauCart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TravelBureauCart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TravelBureauCart query()
 * @method static \Illuminate\Database\Eloquent\Builder|TravelBureauCart whereCapasity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TravelBureauCart whereGolbak($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TravelBureauCart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TravelBureauCart whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TravelBureauCart whereMineralAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TravelBureauCart whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TravelBureauCart whereStoreValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TravelBureauCart whereTowhar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TravelBureauCart whereWheel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TravelBureauCart whereWood($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TravelBureauCart whereWoodAmount($value)
 * @mixin \Eloquent
 */
class TravelBureauCart extends Model
{
    protected $guarded = [];

    public $table = 'travelbureau_carts';

    public $timestamps = false;

    public function requiredItems(): HasMany
    {
        return $this->hasMany(TravelBureauCartRequiredItem::class, 'item_id', 'item_id');
    }

    public function skillRequirements(): HasMany
    {
        return $this->hasMany(SkillRequirement::class, 'item', 'name');
    }
}
