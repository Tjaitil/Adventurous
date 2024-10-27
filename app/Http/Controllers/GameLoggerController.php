<?php

namespace App\Http\Controllers;

use App\Enums\GameLogTypes;
use App\Services\GameLogService;
use Illuminate\Http\Request;

class GameLoggerController extends Controller
{
    public function log(Request $request): \Illuminate\Http\JsonResponse
    {
        $type = $request->enum('type', GameLogTypes::class);
        $message = $request->string('text');

        $request->validate([
            'type' => 'string',
            'text' => 'required|string',
        ]);

        switch ($type) {
            case GameLogTypes::ERROR->value:
                GameLogService::addErrorLog($message);
                break;

            case GameLogTypes::WARNING->value:
                GameLogService::addWarningLog($message);
                break;

            case GameLogTypes::SUCCESS->value:
                GameLogService::addSuccessLog($message);
                break;
            default:
                GameLogService::addInfoLog($message);
                break;
        }

        return response()->json([
            'message' => 'Log added',
        ]);
    }
}
