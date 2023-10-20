<?php

namespace App\Http\Builders;

use App\Http\Resources\SkillsResource;


class SkillsBuilder
{
    private SkillsResource $resource;

    public function __construct($resource = null)
    {
        $this->resource = new SkillsResource($resource);
    }

    public function addFarmerXP(int $xp)
    {
        $this->resource->farmer_xp += $xp;
        return $this;
    }

    public function addMinerXP(int $xp)
    {
        $this->resource->miner_xp += $xp;
        return $this;
    }

    public function addTraderXP(int $xp)
    {
        $this->resource->trader_xp += $xp;
        return $this;
    }

    public function addWarriorXP(int $xp)
    {
        $this->resource->warrior_xp += $xp;
        return $this;
    }

    public static function create($resource = null): static
    {
        return new static($resource);
    }
    public function build()
    {
        return $this->resource;
    }
}
