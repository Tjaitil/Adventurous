<?php

namespace App\actions;

class CanLevelUpAction
{

    /**
     * Check if NPC has enough experience to level up
     *
     * @param int $skill_xp
     * @param int $skill_next_level_xp
     *
     * @return bool
     */
    public function handle(int $skill_xp, int $skill_next_level_xp)
    {
        if ($skill_xp > $skill_next_level_xp) {
            return true;
        } else {
            return false;
        }
    }
}
