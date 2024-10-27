<?php

namespace App\Exceptions;

use App\Http\Responses\AdvResponse;
use App\Services\GameLogService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryFullException extends Exception
{
    public function __construct()
    {
        parent::__construct('Inventory is full', 500);
    }

    public function render(Request $request): JsonResponse
    {
        return (new AdvResponse([], 422))
            ->addMessage(GameLogService::addErrorLog('Inventory is full'))
            ->toResponse($request);
    }
}
