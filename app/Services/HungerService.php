<?php

namespace App\Services;

use App\libs\Response;
use App\Models\Hunger;

class HungerService
{

    private Hunger $Hunger;

    public function __construct(private SessionService $sessionService)
    {
        $this->Hunger = Hunger::find($this->sessionService->user_id());
    }



    /**
     * Get hunger
     *
     * @return int
     */
    public function getCurrentHunger()
    {
        return $this->Hunger->current;
    }



    /**
     * Get hunger data
     *
     * @return array
     */
    public function getHungerData()
    {
        return $this->Hunger;
    }



    /**
     * Check if hunger is too low for action
     *
     * @return bool
     */
    public function isHungerTooLow()
    {
        if ($this->Hunger->current < 10) {
            return true;
        } else {
            return false;
        }
    }



    /**
     * 
     * @return Response 
     */
    public function logHungerTooLow()
    {
        return Response::addMessage("Your hunger bar is too low")->setStatus(422);
    }



    /**
     * Set new hunger based on action
     *
     * @param string $action
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
                # code...
                break;
        }
    }



    /**
     * 
     * @param int $new_hunger 
     * @return void 
     */
    public function updateHunger(int $new_hunger)
    {
        $this->Hunger->current = $new_hunger;
        if ($this->Hunger->current > 100) {
            $this->Hunger->current = 100;
        }

        $this->Hunger->save();
    }



    /**
     * 
     * @param int $amount 
     * @return void 
     */
    public function decreaseHunger(int $amount)
    {
        $this->updateHunger($this->Hunger->current + $amount);
    }



    /**
     * 
     * @param int $amount 
     * @return void 
     */
    public function increaseHunger(int $amount)
    {
        $this->updateHunger($this->Hunger->current - $amount);
    }
}
