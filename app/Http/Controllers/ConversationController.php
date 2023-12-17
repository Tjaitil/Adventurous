<?php

namespace App\Http\Controllers;

use App\Services\ConversationService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConversationController extends Controller
{
    public function __construct(private ConversationService $ConversationService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $person = $request->string('person');
            $nextKey = $request->string('nextKey');
            $isStarting = $request->boolean('is_starting');

            if (! $request->has('person')) {
                throw new Exception('person is required', 400);
            }

            $currentConversationSegment = $this->ConversationService->getConversation($person, $nextKey, $isStarting);

            return response()->json([
                'conversation_segment' => $currentConversationSegment,
            ]);

        } catch (Exception $e) {
            Log::warning('Trying to load a conversation that does not exist', [
                'person' => $person,
                'exception' => $e,
            ]);

            return response()->json([
                'error' => 'Conversation file not found',
            ], 422);
        }
    }
}
