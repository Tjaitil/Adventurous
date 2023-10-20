<?php

namespace App\Http\Controllers;

use App\libs\controller;

class IndexController extends controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        /*$this->render('index', 'index', false);*/
        header("Location: /login");
        exit();
    }
}
