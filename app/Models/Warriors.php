<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warriors extends Model
{
    protected $fillable = ['warrior_id', 'location'];
    public $timestamps = false;

    public $dates = ['training_countdown'];

    public function levels()
    {
        return $this->hasOne(WarriorsLevels::class, 'id', 'id');
    }
}
