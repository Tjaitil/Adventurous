<?php

namespace App\services;

use App\libs\Response;
use App\models\Hunger_model;

class HungerService
{

    private Hunger_model $hunger_model;

    private $hunger_data = null;


    public function __construct(Hunger_model $hunger_model)
    {
        $this->hunger_model = $hunger_model;
    }

    /**
     * Get hunger
     *
     * @return int
     */
    public function getHunger()
    {
        $this->getHungerData();
        return $this->hunger_data['hunger'];
    }

    /**
     * Get hunger data
     *
     * @return array
     */
    public function getHungerData()
    {
        if (is_null($this->hunger_data)) {
            $this->hunger_data = $this->hunger_model->get();
        }
        return $this->hunger_data;
    }

    /**
     * Check if hunger is too low for action
     *
     * @return bool
     */
    public function isHungerTooLow()
    {
        $this->getHungerData();

        if ($this->hunger_data['hunger'] < 10) {
            return true;
        } else {
            return false;
        }
    }

    public function logHungerTooLow()
    {
        return Response::addMessage("Your hunger is too low")->setStatus(422);
    }

    /**
     * Set new hunger based on action
     *
     * @param string $action
     *
     * @return void
     */
    public function setNewHunger($action)
    {
        $this->getHungerData();

        switch ($action) {
            case HUNGER_SKILL_ACTION:
                // TODO: Decrease hunger
                // $this->hunger_data['hunger'] - 15;
            default:
                # code...
                break;
        }

        $this->hunger_model->update($this->hunger_data['hunger']);
    }
}
