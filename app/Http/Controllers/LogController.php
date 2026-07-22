<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogController extends Controller
{
    public function logFrontendError(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'text' => 'required|string',
            'stack' => 'nullable|string',
        ]);

        Log::error($validated['text'], array_filter([
            'stack' => $validated['stack'] ?? null,
        ]));

        return response()->json([], 200);
    }
}
