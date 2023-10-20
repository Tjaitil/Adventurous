<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = ['username', 'password'];
    public $timestamps = false;

    public function user_data()
    {
        return $this->hasOne(User_data::class, 'username', 'username');
    }
}
