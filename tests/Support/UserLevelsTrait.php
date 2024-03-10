<?php

namespace Tests\Support;

use App\Enums\SkillNames;
use App\Models\LevelData;
use App\Models\User;
use App\Models\UserLevels;

trait UserLevelsTrait
{
    public User $TestUser;

    public UserLevels $TestUserLevels;

    public function __constructUserLevelsTrait()
    {
        $this->TestUser = User::find(3);
        $this->TestUserLevels = $this->getUserLevels();
    }

    private function getUserLevels(): UserLevels
    {
        return UserLevels::where('user_id', $this->TestUser->id)->first();
    }

    /**
     * Sets miner level for TestUser
     */
    public function setMinerLevel(int $level): void
    {
        $this->TestUserLevels->miner_level = $level;
        $this->TestUserLevels->save();
    }

    public function incrementMinerLevel(): void
    {
        $this->setMinerLevel($this->TestUserLevels->miner + 1);
    }

    public function setFarmerLevel(int $level): void
    {
        $this->TestUserLevels->farmer_level = $level;
        $this->TestUserLevels->save();
    }

    public function incrementFarmerLevel(): void
    {
        $this->setFarmerLevel($this->TestUserLevels->farmer + 1);
    }

    /**
     * Sets current level of a skill to 'levelupable' state 1 xp before next level
     *
     * @param  value-of<\App\Enums\SkillNames>  $skill
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function setSkillLevelUpAble(string $skill, ?int $levelsFromCurrent = null): void
    {

        switch ($skill) {
            case SkillNames::FARMER->value:
                $currentLevel = $this->TestUserLevels->farmer_level;
                break;
            case SkillNames::MINER->value:
                $currentLevel = $this->TestUserLevels->miner_level;
                break;
            case SkillNames::TRADER->value:
                $currentLevel = $this->TestUserLevels->trader_level;
                break;
            case SkillNames::WARRIOR->value:
                $currentLevel = $this->TestUserLevels->warrior_level;
                break;
            default:
                $currentLevel = 1;
                break;
        }

        $targetLevel = is_null($levelsFromCurrent) ? $currentLevel : $currentLevel + $levelsFromCurrent;

        $xpNextLevel = LevelData::where('level', $targetLevel)->firstOrFail()->next_Level;

        switch ($skill) {
            case SkillNames::FARMER->value:
                $this->TestUserLevels->farmer_xp = $xpNextLevel;
                break;
            case SkillNames::MINER->value:
                $this->TestUserLevels->miner_xp = $xpNextLevel;
                break;
            case SkillNames::TRADER->value:
                $this->TestUserLevels->trader_xp = $xpNextLevel;
                break;
            case SkillNames::WARRIOR->value:
                $this->TestUserLevels->builder_xp = $xpNextLevel;
                break;

            default:
                break;
        }

        $this->TestUserLevels->save();
    }
}
