<?php

namespace App\Http\Controllers;

use App\libs\Response;
use App\Models\UserLevels;
use App\Services\SessionService;

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
