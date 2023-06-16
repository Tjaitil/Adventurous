<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class CityRelation extends Model
{
    protected $timestamps = false;

    protected $table = 'city_relations';

    protected $fillable = [
        'city',
        'hirtam',
        'pvitul',
        'khanz',
        'ter',
        'fansalplains'
    ];
}
