<?php

namespace App\Http\Controllers;

use App\Models\UserLevels;
use App\Services\GameLogService;
use App\Services\SkillsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SkillsController extends Controller
{
    public function __construct(private SkillsService $skillsService) {}

    public function handleUpdate(GameLogService $logService): JsonResponse
    {
        $skills = $this->skillsService->levelUpSkills();

        $UserLevels = UserLevels::where('username', Auth::user()->username)->first()?->toArray();

        $messages = [];
        if ($skills) {
            foreach ($skills as $key => $skill) {
                $message = sprintf('You have leveled up %s to level %d!', $skill['skill'], $skill['new_level']);
                $messages[] = $logService->addSuccessLog($message);

            }
        }

        return response()->jsonWithGameLogs([
            'user_levels' => $UserLevels,
            'new_levels' => $skills,
        ],
            $messages,
            200);
    }
}
