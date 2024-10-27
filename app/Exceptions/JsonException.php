<?php

namespace App\Exceptions;

use App\Http\Responses\AdvResponse;
use App\Services\GameLogService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;

class JsonException extends Exception
{
    public function __construct(
        protected $message,
        protected $code = 500,
    ) {
        parent::__construct($message, $code);
        Log::critical($message, ['username' => Auth::user()?->username]);
    }

    public function render(Request $request): JsonResponse
    {
        return (new AdvResponse([], 500))
            ->addMessage(GameLogService::addErrorLog('Unexpected issue happened'))
            ->toResponse($request);
    }
}
