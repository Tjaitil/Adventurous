<?php

namespace App\Services;

use App\Models\Hunger;

class HungerService
{
    /**
     * Get hunger
     */
    public function getCurrentHunger(int $userId): Hunger
    {
        return Hunger::find($userId)->first();
    }

    /**
     * Check if hunger is too low for action
     */
    public function isHungerTooLow(Hunger $Hunger): bool
    {
        if ($Hunger->current < 10) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return \App\Http\Responses\AdvResponse
     */
    public function logHungerTooLow()
    {
        return advResponse([], 422)
            ->addMessage(GameLogService::addErrorLog('Your hunger status is too low'));
    }

    /**
     * Set new hunger based on action
     */
    public function setNewHunger(Hunger $Hunger, string $action): void
    {
        if ($action === 'skill') {
            $this->decreaseHunger($Hunger, 10);
        }
    }

    public function setHungerForSkillAction(Hunger $Hunger): void
    {
        $this->setNewHunger($Hunger, 'skill');
    }

    public function updateHunger(Hunger $Hunger, int $adjustBy): Hunger
    {
        $Hunger->current += $adjustBy ;
        if ($Hunger->current > 100) {
            $Hunger->current = 100;
        }

        $Hunger->save();

        return $Hunger;
    }

    public function decreaseHunger(Hunger $Hunger, int $amount): Hunger
    {
        return $this->updateHunger($Hunger, +$amount);
    }

    public function increaseHunger(Hunger $Hunger, int $amount): Hunger
    {
        return $this->updateHunger($Hunger, -$amount);
    }
}
