<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * 
 *
 * @property int $id
 * @property string $base
 * @property string $destination
 * @property string $cargo
 * @property string $assignment_amount
 * @property int $time
 * @property string $assignment_type
 * @property string $date_inserted
 * @property-read \App\Models\TraderAssignmentType|null $type
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TraderAssignment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TraderAssignment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TraderAssignment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TraderAssignment whereAssignmentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TraderAssignment whereAssignmentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TraderAssignment whereBase($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TraderAssignment whereCargo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TraderAssignment whereDateInserted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TraderAssignment whereDestination($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TraderAssignment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TraderAssignment whereTime($value)
 * @mixin \Eloquent
 */
class TraderAssignment extends Model
{

    protected $guarded = [];

    public function type(): HasOne
    {
        return $this->hasOne(TraderAssignmentType::class, 'type', 'assignment_type');
    }
}
