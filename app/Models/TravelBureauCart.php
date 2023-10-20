<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
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
