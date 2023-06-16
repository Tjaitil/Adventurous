<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Warriors_levels extends Model
{
    protected $fillable = ['warrior_id'];
    public $timestamps = false;
    public $incrementing = false;

    public function warrior()
    {
        $this->hasOne(Warrior::class, 'id', 'id');
    }
}
