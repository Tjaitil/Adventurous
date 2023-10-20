<?php

namespace App\controllers;

use App\libs\controller;
use \DateTime;

class MainController extends controller
{
    public $gamedata;
    public $data;

    function __construct()
    {
        parent::__construct(false);
    }
    function index()
    {
        \var_dump('hello');
        if (!isset($_SESSION['gamedata'])) {
            $this->fetchData();
            $this->loadModel('CanvasGameID', true);
            $this->model->updateSessionID();
        }
        // // checkLevel in controller
        // $this->checkLevel();
        // $this->loadModel('Main', true);
        // $this->data = $this->model->getData();
        // $this->calculateCountdowns();
        $this->render('main', 'Main', false);
    }
    public function fetchData()
    {
        $this->loadModel('gamedata', true);
        $_SESSION['gamedata'] = $this->model->fetchData();
        $_SESSION['gamedata']['travelling'] = false;
        $profiency = $_SESSION['gamedata']['profiency'];
        $_SESSION['gamedata']['profiency_level'] = $_SESSION['gamedata'][$profiency]['level'];
        $_SESSION['gamedata']['profiency_xp'] = $_SESSION['gamedata'][$profiency]['xp'];
        $_SESSION['gamedata']['profiency_xp_nextlevel'] = $_SESSION['gamedata'][$profiency]['next_level'];
        $_SESSION['gamedata']['conversation']['progress'] = '';
        $_SESSION['log'] = array();
        $_SESSION['log'][] = "Welcome to Adventurous, " . $_SESSION['gamedata']['username'] . "!";
        $this->model->checkMarket();
    }
    public function calculateCountdowns($data = false)
    {
        if ($data !== false) {
            $this->data = $data;
        }
        $date_now = date_timestamp_get(new DateTime(date("Y-m-d H:i:s")));
        foreach ($this->data['farmer_countdowns'] as $key) {
            $countdown = date_timestamp_get(new DateTime($key['crop_countdown']));
            if ($countdown < $date_now && $key['plot1_harvest'] == 1) {
                $this->data['countdowns']['farmer'][] = "Finished";
            } else if ($countdown > $date_now && $key['plot1_harvest'] == 1) {
                $time_left = $countdown - $date_now;
                $m = round($time_left / 60);
                $this->data['countdowns']['farmer'][] = "done in " . $m . 'm';
            } else {
                $this->data['countdowns']['farmer'][] = "nothing happening";
            }
        }
        foreach ($this->data['miner_countdowns'] as $key) {
            $countdown = date_timestamp_get(new DateTime($key['mining_countdown']));
            if ($countdown < $date_now && $key['fetch_minerals'] == 1) {
                $this->data['countdowns']['miner'][] = "Finished";
            } else if ($countdown > $date_now && $key['fetch_minerals'] == 1) {
                $time_left = $countdown - $date_now;
                $h = round($time_left / 60 / 24);
                $m = round($time_left / 60);
                $this->data['countdowns']['miner'][] = "Done in " . $h . 'h : ' . $m . 'm';
            } else {
                $this->data['countdowns']['miner'][] = "Nothing happening";
            }
        }
        $this->data['countdowns']['warrior']['training'] = 0;
        $this->data['countdowns']['warrior']['finished'] = 0;
        $this->data['countdowns']['warrior']['mission'] = 0;
        $this->data['countdowns']['warrior']['idle'] = 0;
        foreach ($this->data['warriors_countdowns'] as $key) {
            $countdown = date_timestamp_get(new DateTime($key['training_countdown']));
            if ($countdown < $date_now && $key['fetch_report'] == 1) {
                $this->data['countdowns']['warrior']['finished'] += 1;
            } else if ($countdown > $date_now && $key['fetch_report'] == 1) {
                $this->data['countdowns']['warrior']['training'] += 1;
            }
            if ($key['mission'] == 1) {
                $this->data['countdowns']['warrior']['mission'] += 1;
            } else {
                $this->data['countdowns']['warrior']['idle'] += 1;
            }
        }
        $this->data['countdowns']['warrior']['mission_countdown'] = $this->data['army_mission']['mission_countdown'];
        $this->data['countdowns']['trader'] = ($this->data['trader_countdown']['assignment_id'] == 0) ?
            "None" : $this->data['trader_countdown']['assignment_id'];
        /*$this->data['countdowns']['warrior']['mission'] = ($this->data['army_mission'] == 0) ?
            "None" : $this->data['army_mission'];*/
        if ($data !== false) {
            return $this->data;
        }
    }
}
