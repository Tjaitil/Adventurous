<?php

namespace App\Services;

use App\libs\Response;
use App\Models\Hunger;
use Illuminate\Support\Facades\Auth;

class HungerService
{
    private ?Hunger $Hunger;

    public function __construct()
    {
    }

    private function getHunger()
    {
        if (! isset($this->Hunger)) {
            $this->Hunger = Hunger::find(Auth::user()->id);
        }
    }

    /**
     * Get hunger
     *
     * @return int
     */
    public function getCurrentHunger()
    {
        $this->getHunger();

        return $this->Hunger->current;
    }

    /**
     * Get hunger data
     *
     * @return array
     */
    public function getHungerData()
    {
        $this->getHunger();

        return $this->Hunger;
    }

    /**
     * Check if hunger is too low for action
     *
     * @return bool
     */
    public function isHungerTooLow()
    {
        $this->getHunger();

        if ($this->Hunger->current < 10) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return Response
     */
    public function logHungerTooLow()
    {
        return Response::addMessage('Your hunger bar is too low')->setStatus(422);
    }

    /**
     * Set new hunger based on action
     *
     *
     * @return void
     */
    public function setNewHunger(string $action)
    {
        switch ($action) {
            case HUNGER_SKILL_ACTION:
                // TODO: Decrease hunger
                $this->decreaseHunger(10);
            default:
                // code...
                break;
        }
    }

    /**
     * @return void
     */
    public function updateHunger(int $new_hunger)
    {
        $this->getHunger();

        $this->Hunger->current = $new_hunger;
        if ($this->Hunger->current > 100) {
            $this->Hunger->current = 100;
        }

        $this->Hunger->save();
    }

    /**
     * @return void
     */
    public function decreaseHunger(int $amount)
    {
        $this->updateHunger($this->Hunger->current + $amount);
    }

    /**
     * @return void
     */
    public function increaseHunger(int $amount)
    {
        $this->updateHunger($this->Hunger->current - $amount);
    }
}
