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
 * @method static \Illuminate\Database\Eloquent\Builder|SkillRequirement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SkillRequirement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SkillRequirement query()
 * @method static \Illuminate\Database\Eloquent\Builder|SkillRequirement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillRequirement whereItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillRequirement whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillRequirement whereSkill($value)
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
