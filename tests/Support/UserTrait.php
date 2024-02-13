<?php

namespace Tests\Support;

use App\Enums\GameMaps;
use App\Enums\SkillNames;
use App\Models\LevelData;
use App\Models\User;
use App\Models\UserData;
use App\Models\UserLevels;

trait UserTrait
{
    private ?User $User;

    public User $RandomUser;

    public function __constructUserTrait()
    {
        $this->User = User::find(3);
        $this->RandomUser = $this->getRandomUser();
    }

    /**
     * @return void
     */
    public function setUserData(array $user_data, ?int $user_id)
    {
        $user_id = $user_id ?? 2;
        UserData::where('id', $user_id)->update($user_data);
    }

    public function getRandomUser(): User
    {
        return User::find(3);
    }

    /**
     * @return void
     */
    public function setUserCurrentLocation(string $location, User $User)
    {
        $UserData = UserData::where('id', $User->id)->first();
        if (! $UserData instanceof UserData) {
            throw new \Exception('User data not found');
        }

        $map_locations = \array_flip(GameMaps::locationMapping());

        $map_location = $map_locations[$location];
        if (! $map_location) {
            throw new \Exception('Location not found');
        }

        $UserData->location = $location;
        $UserData->map_location = $map_location;
        $UserData->save();
    }

    /**
     * @param  value-of<\App\Enums\SkillNames>  $skill
     */
    public function setUserLevel(string $skill, int $level, User $User)
    {
        $Skills = $User->userLevels;

        if (! $Skills instanceof UserLevels) {
            throw new \Exception('User data not found');
        }

        if ($level === 1) {
            $xp = 0;
        } else {
            $xp = LevelData::where('level', $level - 1)->firstOrFail()->next_Level;
        }

        switch ($skill) {
            case SkillNames::FARMER->value:
                $Skills->farmer_level = $level;
                $Skills->farmer_xp = $xp;
                break;
            case SkillNames::MINER->value:
                $Skills->miner_level = $level;
                $Skills->miner_xp = $xp;
                break;
            case SkillNames::TRADER->value:
                $Skills->trader_level = $level;
                $Skills->trader_xp = $xp;
                break;
            case SkillNames::WARRIOR->value:
                $Skills->builder_level = $level;
                $Skills->builder_xp = $xp;
                break;

            default:
                break;
        }

        $Skills->save();
    }
}
