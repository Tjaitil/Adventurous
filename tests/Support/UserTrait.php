<?php

namespace Tests\Support;

use App\Enums\GameMaps;
use App\Models\User;
use App\Models\UserData;

trait UserTrait
{
    private ?User $User;

    public User $RandomUser;

    public function __constructUserTrait()
    {
        $this->User = User::find(1);
        $this->RandomUser = $this->getRandomUser();
    }

    /**
     * @return void
     */
    public function setUserData(array $user_data, ?int $user_id)
    {
        $user_id = $user_id ?? 1;
        UserData::where('id', $user_id)->update($user_data);
    }

    public function getRandomUser(): User
    {
        return User::find(1);
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
}
