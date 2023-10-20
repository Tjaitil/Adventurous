<?php

namespace App\Http\Controllers;

use App\libs\controller;
use App\libs\Response;
use App\Services\WarriorService;

class ArmymissionsController extends controller
{
    public $data;

    function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $this->loadModel("ArmyMissions", true);
        $this->data = $this->model->getData(false);
        $Warriors_model = $this->loadModel("Warriors", true, true);
        $this->data["warriors"] = $Warriors_model->getAvailableWarriors(false);
        $this->render('armymissions', 'Army-Missions', $this->data, true, true);
    }
}
