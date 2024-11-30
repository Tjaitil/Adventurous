<?php

namespace App\Actions;

class CanLevelUpAction
{
    /**
     * Check if NPC has enough experience to level up
     *
     *
     * @return bool
     */
    public function handle(int $skill_xp, ?int $skill_next_level_xp)
    {
        if (is_null($skill_next_level_xp) || $skill_xp >= $skill_next_level_xp) {
            return true;
        } else {
            return false;
        }
    }
}
