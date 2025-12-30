<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SkillRequirement
 *
 * @property int $id
 * @property string $item
 * @property string $skill
 * @property int $level
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillRequirement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillRequirement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillRequirement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillRequirement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillRequirement whereItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillRequirement whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillRequirement whereSkill($value)
 * @mixin \Eloquent
 */
class SkillRequirement extends Model
{
    public $timestamps = false;

    public function __construct()
    {
        parent::__construct();
    }
}
