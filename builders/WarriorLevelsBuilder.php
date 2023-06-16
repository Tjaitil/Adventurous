<?php

namespace App\builders;

use App\resources\WarriorLevelsResource;


class WarriorLevelsBuilder
{
    private WarriorLevelsResource $resource;

    public function __construct($resource = null)
    {
        $this->resource = new WarriorLevelsResource($resource);
    }

    public static function create($resource = null): static
    {
        return new static($resource);
    }

    public function addToStaminaXP(int $value)
    {
        $this->resource->stamina_xp += $value;
        return $this;
    }

    public function addToStrengthXP(int $value)
    {
        $this->resource->strength_xp += $value;
        return $this;
    }

    public function addToTechniqueXP(int $value)
    {
        $this->resource->technique_xp += $value;
        return $this;
    }

    public function addToPrecisionXP(int $value)
    {
        $this->resource->precision_xp += $value;
        return $this;
    }

    public function build()
    {
        return $this->resource;
    }
}
