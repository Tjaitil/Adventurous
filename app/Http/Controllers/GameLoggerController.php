<?php

namespace App\Http\Controllers;

use App\Enums\GameLogTypes;
use App\Traits\GameLogger;
use Illuminate\Http\Request;

class GameLoggerController extends Controller
{
    use GameLogger;

    public function log(Request $request): \Illuminate\Http\JsonResponse
    {
        $type = $request->string('type');
        $message = $request->string('text');

        $request->validate([
            'type' => 'string',
            'text' => 'required|string',
        ]);

        switch ($type) {
            case GameLogTypes::ERROR->value:
                $this->addErrorMessage($message);
                break;

            case GameLogTypes::WARNING->value:
                $this->addWarningMessage($message);
                break;

            case GameLogTypes::SUCCESS->value:
                $this->addSuccessMessage($message);
                break;
            default:
                $this->addInfoMessage($message);
                break;
        }

        return response()->json([
            'message' => 'Log added',
        ]);
    }
}
