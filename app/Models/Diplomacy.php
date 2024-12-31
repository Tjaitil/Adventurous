<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $username
 * @property string $hirtam
 * @property string $pvitul
 * @property string $khanz
 * @property string $ter
 * @property string $fansalplains
 * @property int $user_id
 * @method static \Database\Factories\DiplomacyFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Diplomacy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Diplomacy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Diplomacy query()
 * @method static \Illuminate\Database\Eloquent\Builder|Diplomacy whereFansalplains($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Diplomacy whereHirtam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Diplomacy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Diplomacy whereKhanz($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Diplomacy wherePvitul($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Diplomacy whereTer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Diplomacy whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Diplomacy whereUsername($value)
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
