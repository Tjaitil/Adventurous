<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class UserData extends Model
{
    public $timestamps = false;

    public function __construct()
    {
        parent::__construct();
    }
}
