<?php

namespace App\services;

use App\actions\CanLevelUpAction;
use App\builders\SkillsBuilder;
use App\enums\SkillNames;
use App\libs\Response;
use App\models\LevelData;
use App\models\UserLevels;
use Exception;

/**
 * @property UserLevels $userLevels
 */
class SkillsService
{
    public SkillsBuilder $skillsBuilder;

    public function __construct(
        private CanLevelUpAction $canLevelUpAction,
        private SessionService $sessionService,
        public UserLevels $userLevels
    ) {
        $this->userLevels = $this->userLevels->where('username', $this->sessionService->getCurrentUsername())->first();
    }

    /**
     * Check if user has required profiency level
     *
     * @param int $required_level Required profiency level
     * @param string $skill Name of skill
     *
     * @return bool
     */
    public function hasRequiredLevel(int $required_level, string $skill)
    {

        switch ($skill) {
            case 'farmer':
                $skill_level = $this->userLevels->farmer_level;
                break;

            case 'miner':
                $skill_level = $this->userLevels->miner_level;
                break;

            case 'trader':
                $skill_level = $this->userLevels->trader_level;
                break;

            case 'warrior':
                $skill_level = $this->userLevels->warrior_level;
                break;

            default:
                $skill_level = 0;
                break;
        }
        if ($required_level <= $skill_level) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Log that user has too low profiency level
     *
     * @param string $profiency Profiency name
     *
     * @return Response
     */
    public function logNotRequiredLevel(string $profiency)
    {
        return Response::addMessage(sprintf("You have too low %s level", $profiency))->setStatus(422);
    }

    public function updateSkills()
    {
        $this->userLevels->update();
        $this->canSkillsLevelUP();
    }

    public function updateFarmerXP(int $amount)
    {
        $this->userLevels->farmer_xp += $amount;
        return $this;
    }

    public function updateMinerXP(int $amount)
    {
        $this->userLevels->miner_xp += $amount;
        return $this;
    }

    public function updateTraderXP(int $amount)
    {
        $this->userLevels->trader_xp += $amount;
        return $this;
    }

    public function updateWarriorXP(int $amount)
    {
        $this->userLevels->warrior_xp += $amount;
        return $this;
    }

    public function canSkillsLevelUP()
    {

        if ($this->canLevelUpAction->handle($this->userLevels->farmer_xp, $this->userLevels->farmer_next_level_xp)) {

            $this->userLevels->farmer_level = $this->getNextLevelFromExperience($this->userLevels->farmer_xp);
            Response::addLevelUP(SkillNames::FARMER->value, $this->userLevels->farmer_level);
        }

        if ($this->canLevelUpAction->handle($this->userLevels->miner_xp, $this->userLevels->miner_next_level_xp)) {

            $this->userLevels->miner_level = $this->getNextLevelFromExperience($this->userLevels->miner_xp);
            Response::addLevelUP(SkillNames::MINER->value, $this->userLevels->miner_level);
        }

        if ($this->canLevelUpAction->handle($this->userLevels->trader_xp, $this->userLevels->trader_next_level_xp)) {

            $this->userLevels->trader_level = $this->getNextLevelFromExperience($this->userLevels->trader_xp);
            Response::addLevelUP(SkillNames::TRADER->value, $this->userLevels->trader_level);
        }

        if ($this->canLevelUpAction->handle($this->userLevels->warrior_xp, $this->userLevels->warrior_next_level_xp)) {

            $this->userLevels->warrior_level = $this->getNextLevelFromExperience($this->userLevels->warrior_xp);
            Response::addLevelUP(SkillNames::WARRIOR->value, $this->userLevels->warrior_level);
        }
    }

    /**
     * Retrieve data from LevelData table based on current_skill_experience
     *
     * @param int $current_skill_experience
     *
     * @return int
     */
    private function getNextLevelFromExperience(int $current_skill_experience)
    {
        $data = LevelData::select('level')->where('next_level', '>', $current_skill_experience)->limit(1)->first();
        if (is_null($data->level)) {
            throw new Exception("Unable to get level from experience");
        } else {
            return $data->level;
        }
    }
}
