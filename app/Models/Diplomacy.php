<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $username
 * @property int $user_id
 * @property float $hirtam
 * @property float $pvitul
 * @property float $khanz
 * @property float $ter
 * @property float $fansal_plains
 * @method static \Database\Factories\DiplomacyFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diplomacy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diplomacy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diplomacy query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diplomacy whereFansalPlains($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diplomacy whereHirtam($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diplomacy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diplomacy whereKhanz($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diplomacy wherePvitul($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diplomacy whereTer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diplomacy whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Diplomacy whereUsername($value)
 * @mixin \Eloquent
 */
class Diplomacy extends Model
{
    /**
     * @use HasFactory<\Database\Factories\DiplomacyFactory>
     */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [];

    protected $table = 'diplomacy';
}
