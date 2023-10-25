<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = ['username', 'password'];

    public $timestamps = false;

    public function user_data()
    {
        return $this->hasOne(User_data::class, 'username', 'username');
    }
}
