<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * 
 * @property int $id
 * @property string $cargo
 * @property string $base
 * @property string $destination
 * @property string $cargo
 * @property int $assignment_amount
 * @property int $time
 * @property string $assignment_type
 * @property \Carbon\Carbon $date_inserted
 * @property TraderAssignmentType $type
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
