<?php

namespace App\controllers;

use App\libs\controller;
use App\libs\Response;
use App\models\UserLevels;
use App\services\SessionService;

class HighscoresController extends controller
{
    public $data;

    function __construct(private SessionService $sessionService)
    {
        parent::__construct();
    }

    public function index()
    {
        $this->loadModel('Highscores', true);
        $this->data = $this->model->getData();
        $this->render('highscores', 'Highscores', $this->data);
    }

    /**
     * Get levels for a specific user
     *
     * @return Response
     */
    public function show()
    {
        $levels = UserLevels::where('user_id', $this->sessionService->getCurrentUsername())
            ->first()
            ->toArray();

        return Response::setResponse($levels);
    }
}
