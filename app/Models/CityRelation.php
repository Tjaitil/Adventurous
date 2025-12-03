<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property string $city
 * @property string $hirtam
 * @property string $pvitul
 * @property string $khanz
 * @property string $ter
 * @property string $fansal_plains
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CityRelation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CityRelation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CityRelation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CityRelation whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CityRelation whereFansalPlains($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CityRelation whereHirtam($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CityRelation whereKhanz($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CityRelation wherePvitul($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CityRelation whereTer($value)
 * @mixin \Eloquent
 */
class CityRelation extends Model
{
    public $timestamps = false;

    protected $table = 'city_relations';
}
