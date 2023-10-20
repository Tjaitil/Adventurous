<?php

namespace App\builders;

use App\enums\GameLocations;
use App\resources\CountdownResource;
use App\resources\TimeResource;
use App\resources\WarriorLevelsResource;
use App\resources\WarriorResource;
use GameConstants;


class WarriorBuilder
{
    private WarriorResource $resource;

    public function __construct($resource = null)
    {
        $this->resource = new WarriorResource($resource);
    }


    public static function create($resource = null): static
    {
        return new static($resource);
    }

    public function setStaminaLevel(int $level)
    {
        $this->resource->levels->stamina_level = $level;
        return $this;
    }

    public function setStaminaXP(int $xp)
    {
        $this->resource->levels->stamina_xp = $xp;
        return $this;
    }

    public function setStrengthLevel(int $level)
    {
        $this->resource->levels->strength_level = $level;
        return $this;
    }

    public function setStrengthXP(int $xp)
    {
        $this->resource->levels->strength_xp = $xp;
        return $this;
    }

    public function setTechniqueLevel(int $level)
    {
        $this->resource->levels->technique_level = $level;
        return $this;
    }

    public function setTechniqueXP(int $xp)
    {
        $this->resource->levels->technique_xp = $xp;
        return $this;
    }

    public function setPrecisionLevel(int $level)
    {
        $this->resource->levels->precision_level = $level;
        return $this;
    }

    public function setPrecisionXP(int $xp)
    {
        $this->resource->levels->precision_xp = $xp;
        return $this;
    }

    public function setHelm(string $value)
    {
        $this->resource->helm = $value;
        return $this;
    }

    public function setBody(string $value)
    {
        $this->resource->body = $value;
        return $this;
    }

    public function setRightHand(string $value)
    {
        $this->resource->right_hand = $value;
        return $this;
    }

    public function setLeftHand(string $value)
    {
        $this->resource->left_hand = $value;
        return $this;
    }

    public function setLegs(string $value)
    {
        $this->resource->legs = $value;
        return $this;
    }

    public function setBoots(string $value)
    {
        $this->resource->boots = $value;
        return $this;
    }

    public function setAmmunitionType(string $value)
    {
        $this->resource->ammunition = $value;
        return $this;
    }

    public function setAmmunitionAmount(string $value)
    {
        $this->resource->ammunition_amount = $value;
        return $this;
    }

    public function addAmmunitionAmount(string $value)
    {
        $this->resource->ammunition += $value;
        return $this;
    }

    public function setWariorLevels(WarriorLevelsResource $value)
    {
        $this->resource->levels = $value;
        return $this;
    }

    public function setWarriorID($id)
    {
        $this->resource->warrior_id = $id;
        return $this;
    }

    public function setArmyMission(int $id)
    {
        $this->resource->army_mission = $id;
        return $this;
    }

    public function setHealth(int $health)
    {
        $this->resource->health;
        return $this;
    }

    public function setAttack(int $attack)
    {
        $this->resource->attack = $attack;
        return $this;
    }

    public function setDefence(int $defence)
    {
        $this->resource->defence = $defence;
        return $this;
    }

    /**
     *
     * @param str $location
     *
     * @return void
     */
    public function setLocation(string $location)
    {
        if (array_search($location, GameLocations::getWarriorLocations()) !== false) {
            $this->resource->location = $location;
            return $this;
        }
        return $this;
    }

    public function setType(string $type)
    {
        if (array_search($type, GameConstants::WARRIOR_TYPES) !== false) {
            $this->resource->type = $type;
        }
        return $this;
    }

    public function setRest(bool $value)
    {
        $this->resource->rest = $value;
        return $this;
    }

    public function setRestCountdown(TimeResource $resource)
    {
        $this->resource->rest_start = $resource;
        return $this;
    }

    public function setTrainingCountdown(TimeResource $resource)
    {
        $this->resource->training_countdown = $resource;
        return $this;
    }

    public function setFetchReport(int $value)
    {
        $this->resource->fetch_report = $value;
        return $this;
    }

    public function setTrainingType(string $value)
    {
        $this->resource->training_type = $value;
        return $this;
    }

    public function build()
    {
        return $this->resource;
    }
}
