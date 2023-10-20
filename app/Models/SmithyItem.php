<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmithyItem extends Model
{
    public $timestamps = false;

    protected $table = 'smithy_items';

    public function requiredItems()
    {
        return $this->hasMany(SmithyItemRequired::class, 'item_id', 'item_id');
    }


    public function skillRequirements()
    {
        return $this->hasMany(SkillRequirement::class, 'item', 'item');
    }
}
