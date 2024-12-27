<?php

namespace App\Updaters;

use App\Models\UserLevels;

final class UserLevelsUpdater
{
    public function __construct(readonly protected UserLevels $userLevels) {}

    public function addFarmerXP(int $xp): self
    {
        $this->userLevels->farmer_xp += $xp;

        return $this;
    }

    public function setFarmerLevel(int $level): self
    {
        $this->userLevels->farmer_level = $level;

        return $this;
    }

    public function addMinerXP(int $xp): self
    {
        $this->userLevels->miner_xp += $xp;

        return $this;
    }

    public function setMinerLevel(int $level): self
    {
        $this->userLevels->miner_level = $level;

        return $this;
    }

    public function addTraderXP(int $xp): self
    {
        $this->userLevels->trader_xp += $xp;

        return $this;
    }

    public function setTraderLevel(int $level): self
    {
        $this->userLevels->warrior_level = $level;

        return $this;
    }

    public function addWarriorXP(int $xp): self
    {
        $this->userLevels->warrior_xp += $xp;

        return $this;
    }

    public function setWarriorLevel(int $level): self
    {
        $this->userLevels->warrior_level = $level;

        return $this;
    }

    public function update(): self
    {
        $this->userLevels->save();

        return $this;
    }

    public static function create(UserLevels $Userlevels): static
    {
        return new self($Userlevels);
    }
}
