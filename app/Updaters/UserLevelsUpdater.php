<?php

namespace App\Updaters;

use App\Models\UserLevels;

final class UserLevelsUpdater
{
    public function __construct(readonly protected UserLevels $userLevels) {}

    public function addFarmerXP(int $xp)
    {
        $this->userLevels->farmer_xp += $xp;

        return $this;
    }

    public function addFarmerLevel(int $level)
    {
        $this->userLevels->farmer_level += $level;

        return $this;
    }

    public function addMinerXP(int $xp)
    {
        $this->userLevels->miner_xp += $xp;

        return $this;
    }

    public function addMinerLevel(int $level)
    {
        $this->userLevels->miner_level += $level;

        return $this;
    }

    public function addTraderXP(int $xp)
    {
        $this->userLevels->trader_xp += $xp;

        return $this;
    }

    public function addTraderLevel(int $level)
    {
        $this->userLevels->warrior_level += $level;

        return $this;
    }

    public function addWarriorXP(int $xp)
    {
        $this->userLevels->warrior_xp += $xp;

        return $this;
    }

    public function addWarriorLevel(int $level)
    {
        $this->userLevels->warrior_level += $level;

        return $this;
    }

    public function update()
    {
        $this->userLevels->save();

        return $this;
    }

    public static function create(UserLevels $Userlevels): static
    {
        return new static($Userlevels);
    }
}
