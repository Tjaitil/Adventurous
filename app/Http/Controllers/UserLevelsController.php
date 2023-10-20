<?php

namespace App\controllers;

use App\libs\Response;
use App\models\UserLevels;
use App\services\SessionService;

class UserLevelsController
{

    public function __construct(private SessionService $sessionService)
    {
    }

    /**
     *
     * @return Response
     */
    public function getLevels()
    {
        $levels = UserLevels::where('username', $this->sessionService->getCurrentUsername())
            ->first()
            ->toArray();

        return Response::setResponse($levels);
    }
}
