<?php

namespace App\actions;

class CalculateCharacterHealthAction
{

    /**
     * Calculate new health for a game character
     *
     * @param int $current_health
     * @param int $max_health
     * @param int $healing_per_item
     * @param int $assigned_amount
     *
     * @return array
     */
    public function handle(int $current_health, int $max_health, int $healing_per_item, int $assigned_amount)
    {
        $used_amount = 0;
        $healing_amount = $healing_per_item;
        while ($current_health < $max_health && $used_amount <= $assigned_amount) {
            $current_health += $healing_amount;
            $used_amount++;
        }

        if ($current_health > $max_health) {
            $current_health = $max_health;
        }

        return [
            'new_health' => $current_health,
            'used_amount' => $used_amount,
        ];
    }
}
