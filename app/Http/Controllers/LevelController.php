<?php

namespace App\Http\Controllers;

use App\Http\Responses\AdvResponse;
use App\Services\SkillsService;

class LevelController extends Controller
{
    public function __construct(private SkillsService $skillsService)
    {

    }

    public function checkLevel(): AdvResponse
    {
        $skills = $this->skillsService->levelUpSkills();
        if ($skills) {
            $response = advResponse(['new_levels' => $skills]);
            foreach ($skills as $key => $skill) {
                $message = sprintf('You have leveled up %s to level %d!', $skill['skill'], $skill['new_level']);
                $response->addSuccessMessage($message);
            }

            return $response;
        } else {
            return advResponse();
        }
    }
}
