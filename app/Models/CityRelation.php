<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $city
 * @property float $hirtam
 * @property float $pvitul
 * @property float $khanz
 * @property float $ter
 * @property float $fansalplains
 * @mixin \Eloquent
 */
class CityRelation extends Model
{
    public $timestamps = false;

    protected $table = 'city_relations';
}
