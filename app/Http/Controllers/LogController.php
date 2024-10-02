<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogController extends Controller
{
    public function logFrontendError(Request $request): JsonResponse
    {
        $text = $request->input('text');
        Log::error($text);

        return response()->json([], 200);
    }
}
