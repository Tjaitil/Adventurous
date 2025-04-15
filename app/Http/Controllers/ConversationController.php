<?php

namespace App\Http\Controllers;

use App\Services\ConversationService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConversationController extends Controller
{
    public function __construct(private ConversationService $ConversationService) {}

    public function index(Request $request): JsonResponse
    {
        $person = null;
        try {
            $person = $request->string('person')->toString();
            $nextKey = $request->integer('selected_option');
            $isStarting = $request->boolean('is_starting');

            if ($person === '') {
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

            return response()->json([], 422);
        }
    }
}
