<?php

namespace App\Services;

use App\Models\Hunger;
use Illuminate\Support\Facades\Auth;

class HungerService
{
    private ?Hunger $Hunger;

    private function getHunger(): void
    {
        if (! isset($this->Hunger)) {
            $this->Hunger = Hunger::find(Auth::user()->id);
        }
    }

    /**
     * Get hunger
     */
    public function getCurrentHunger(): int
    {
        $this->getHunger();

        return $this->Hunger->current;
    }

    /**
     * Get hunger data
     */
    public function getHungerData(): ?Hunger
    {
        $this->getHunger();

        return $this->Hunger;
    }

    /**
     * Check if hunger is too low for action
     */
    public function isHungerTooLow(): bool
    {
        $this->getHunger();

        if ($this->Hunger->current < 10) {
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
        return advResponse([], 422)->addErrorMessage('Your hunger bar is too low');
    }

    /**
     * Set new hunger based on action
     *
     * @param  'skill'  $action
     */
    public function setNewHunger(string $action): void
    {
        if ($action === 'skill') {
            $this->decreaseHunger(10);
        }
    }

    public function setHungerForSkillAction(): void
    {
        $this->setNewHunger('skill');
    }

    public function updateHunger(int $new_hunger): void
    {
        $this->getHunger();

        $this->Hunger->current = $new_hunger;
        if ($this->Hunger->current > 100) {
            $this->Hunger->current = 100;
        }

        $this->Hunger->save();
    }

    public function decreaseHunger(int $amount): void
    {
        $this->updateHunger($this->Hunger->current + $amount);
    }

    public function increaseHunger(int $amount): void
    {
        $this->updateHunger($this->Hunger->current - $amount);
    }
}
