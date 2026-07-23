<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CrashReportController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'error_message' => 'required|string',
            'stack_trace'   => 'nullable|string',
            'game_state'    => 'nullable|array',
            'environment'   => 'required|string|in:dev,prod',
        ]);

        $report = [
            'error_message' => $request->string('error_message')->toString(),
            'stack_trace'   => $request->input('stack_trace'),
            'game_state'    => $request->input('game_state'),
            'environment'   => $request->string('environment')->toString(),
            'user_id'       => $request->user()->id,
            'reported_at'   => now()->toIso8601String(),
        ];

        $filename = now()->timestamp . '_' . Str::random(6) . '.json';
        Storage::put('crash-reports/' . $filename, json_encode($report, JSON_PRETTY_PRINT));

        return response()->json(['success' => true]);
    }
}
