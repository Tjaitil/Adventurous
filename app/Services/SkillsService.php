<?php

namespace App\Services;

use App\Actions\CanLevelUpAction;
use App\Enums\GameEvents;
use App\Enums\SkillNames;
use App\Exceptions\JsonException;
use App\Http\Builders\SkillsBuilder;
use App\Http\Responses\AdvResponse;
use App\Models\LevelData;
use App\Models\UserLevels;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @property UserLevels $userLevels
 */
class SkillsService
{
    public SkillsBuilder $skillsBuilder;

    public UserLevels $userLevels;

    public function __construct(
        private CanLevelUpAction $canLevelUpAction,
    ) {
    }

    private function setUserLevels(): void
    {
        if (! isset($this->userLevels)) {
            $UserLevels = UserLevels::where('username', Auth::user()?->username)->first();
            if (! $UserLevels instanceof UserLevels) {
                throw new JsonException('UserLevels could not be found for user');
            }
            $this->userLevels = $UserLevels;
        }

    }

    /**
     * Check if user has required profiency level
     *
     * @param  int  $required_level  Required profiency level
     * @param  string  $skill  Name of skill
     */
    public function hasRequiredLevel(int $required_level, string $skill): bool
    {
        $this->setUserLevels();
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
     * @param  string  $profiency  Profiency name
     */
    public function logNotRequiredLevel(string $profiency): JsonResponse
    {
        return (new AdvResponse([], 422))
            ->addErrorMessage(sprintf('You have too low %s level', $profiency))
            ->toResponse(request());
    }

    public function updateSkills(?AdvResponse &$advResponse = null): void
    {
        $this->setUserLevels();

        $this->userLevels->save();

        $advResponse->addEvent(GameEvents::XpGainedEvent->value);
    }

    public function updateFarmerXP(int $amount): self
    {
        $this->setUserLevels();
        $this->userLevels->farmer_xp += $amount;

        return $this;
    }

    public function updateMinerXP(int $amount): self
    {
        $this->setUserLevels();
        $this->userLevels->miner_xp += $amount;

        return $this;
    }

    public function updateTraderXP(int $amount): self
    {
        $this->setUserLevels();
        $this->userLevels->trader_xp += $amount;

        return $this;
    }

    public function updateWarriorXP(int $amount): self
    {
        $this->setUserLevels();
        $this->userLevels->warrior_xp += $amount;

        return $this;
    }

    /**
     * @return array<int, array{
     *  'skill': value-of<\App\Enums\SkillNames>,
     *  'new_level': int
     * }>
     */
    public function levelUpSkills(): array
    {
        $this->setUserLevels();

        $skills = [];

        if ($this->canLevelUpAction->handle($this->userLevels->farmer_xp, $this->userLevels->farmer_next_level_xp)) {

            $this->userLevels->farmer_level = $this->getNextLevelFromExperience($this->userLevels->farmer_xp);
            $skills[] = [
                'skill' => SkillNames::FARMER->value,
                'new_level' => $this->userLevels->farmer_level,
            ];
        }

        if ($this->canLevelUpAction->handle($this->userLevels->miner_xp, $this->userLevels->miner_next_level_xp)) {

            $this->userLevels->miner_level = $this->getNextLevelFromExperience($this->userLevels->miner_xp);
            $skills[] = [
                'skill' => SkillNames::MINER->value,
                'new_level' => $this->userLevels->miner_level,
            ];
        }

        if ($this->canLevelUpAction->handle($this->userLevels->trader_xp, $this->userLevels->trader_next_level_xp)) {

            $this->userLevels->trader_level = $this->getNextLevelFromExperience($this->userLevels->trader_xp);
            $skills[] = [
                'skill' => SkillNames::TRADER->value,
                'new_level' => $this->userLevels->trader_level,
            ];
        }

        if ($this->canLevelUpAction->handle($this->userLevels->warrior_xp, $this->userLevels->warrior_next_level_xp)) {

            $this->userLevels->warrior_level = $this->getNextLevelFromExperience($this->userLevels->warrior_xp);
            $skills[] = [
                'skill' => SkillNames::WARRIOR->value,
                'new_level' => $this->userLevels->warrior_level,
            ];
        }

        $this->userLevels->save();

        return $skills;
    }

    /**
     * Retrieve data from LevelData table based on current_skill_experience
     *
     * @return int
     *
     * @throws Exception
     */
    private function getNextLevelFromExperience(int $current_skill_experience)
    {
        $LevelData = LevelData::select('level')->where('next_level', '>', $current_skill_experience)->limit(1)->first();
        if (! $LevelData instanceof LevelData) {
            throw new JsonException('Unable to get level from experience');
        } else {
            return $LevelData->level;
        }
    }
}
