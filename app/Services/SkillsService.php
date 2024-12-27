<?php

namespace App\Services;

use App\Actions\CanLevelUpAction;
use App\Enums\SkillNames;
use App\Exceptions\JsonException;
use App\Http\Builders\SkillsBuilder;
use App\Http\Responses\AdvResponse;
use App\Models\LevelData;
use App\Models\UserLevels;
use App\Updaters\UserLevelsUpdater;
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
    ) {}

    private function setUserLevels(): void
    {
        if (! isset($this->userLevels)) {
            $UserLevels = UserLevels::where('id', Auth::user()?->id)->first();
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
    public function hasRequiredLevel(UserLevels $UserLevels, int $required_level, string $skill): bool
    {
        switch ($skill) {
            case 'farmer':
                $skill_level = $UserLevels->farmer_level;
                break;

            case 'miner':
                $skill_level = $UserLevels->miner_level;
                break;

            case 'trader':
                $skill_level = $UserLevels->trader_level;
                break;

            case 'warrior':
                $skill_level = $UserLevels->warrior_level;
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
            ->addMessage(GameLogService::addErrorLog(sprintf('You have too low %s level', $profiency)))
            ->toResponse(request());
    }

    /**
     * @return array<int, array{
     *  'skill': value-of<\App\Enums\SkillNames>,
     *  'new_level': int
     * }>
     */
    public function levelUpSkills(UserLevels $UserLevels): array
    {
        $skills = [];

        $UsersLevelsUpdater = UserLevelsUpdater::create($UserLevels);
        if ($this->canLevelUpAction->handle($UserLevels->farmer_xp, $UserLevels->farmer_next_level_xp)) {

            $level = $this->getNextLevelFromExperience($UserLevels->farmer_xp);
            $UsersLevelsUpdater->setFarmerLevel($level);
            $skills[] = [
                'skill' => SkillNames::FARMER->value,
                'new_level' => $UserLevels->farmer_level,
            ];
        }

        if ($this->canLevelUpAction->handle($UserLevels->miner_xp, $UserLevels->miner_next_level_xp)) {

            $level = $this->getNextLevelFromExperience($UserLevels->miner_xp);
            $UsersLevelsUpdater->setMinerLevel($level);
            $skills[] = [
                'skill' => SkillNames::MINER->value,
                'new_level' => $UserLevels->miner_level,
            ];
        }

        if ($this->canLevelUpAction->handle($UserLevels->trader_xp, $UserLevels->trader_next_level_xp)) {

            $level = $this->getNextLevelFromExperience($UserLevels->trader_xp);
            $UsersLevelsUpdater->setTraderLevel($level);

            $skills[] = [
                'skill' => SkillNames::TRADER->value,
                'new_level' => $UserLevels->trader_level,
            ];
        }

        if ($this->canLevelUpAction->handle($UserLevels->warrior_xp, $UserLevels->warrior_next_level_xp)) {

            $level = $this->getNextLevelFromExperience($UserLevels->warrior_xp);

            $UsersLevelsUpdater->setWarriorLevel($level);
            $skills[] = [
                'skill' => SkillNames::WARRIOR->value,
                'new_level' => $UserLevels->warrior_level,
            ];
        }

        $UsersLevelsUpdater->update();

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
