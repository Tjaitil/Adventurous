<?php

namespace App\Http\Controllers;

use App\libs\controller;

class notfound extends controller
{

    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $this->render('error', 'Error', false);
    }
}
