<?php

namespace App\Http\Controllers;

use App\Http\Responses\AdvResponse;
use App\Models\UserLevels;
use App\Services\SkillsService;
use Illuminate\Support\Facades\Auth;

class SkillsController extends Controller
{
    public function __construct(private SkillsService $skillsService)
    {
    }

    public function handleUpdate(): AdvResponse
    {
        $skills = $this->skillsService->levelUpSkills();

        $UserLevels = UserLevels::where('username', Auth::user()->username)->first()?->toArray();

        $response = advResponse([
            'user_levels' => $UserLevels,
            'new_levels' => $skills,
        ]);

        if ($skills) {
            foreach ($skills as $key => $skill) {
                $message = sprintf('You have leveled up %s to level %d!', $skill['skill'], $skill['new_level']);
                $response->addSuccessMessage($message);
            }
        }

        return $response;
    }
}
