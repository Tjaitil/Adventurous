<?php

namespace Tests\Support;

use App\Enums\GameMaps;
use App\Models\User;
use App\Models\UserData;

trait UserTrait
{
    private ?User $User;

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
        return User::inRandomOrder()->first();
    }

    /**
     * @return void
     */
    public function setUserCurrentLocation(string $location, int $user_id = null)
    {
        $map_locations = \array_flip(GameMaps::locationMapping());

        $map_location = $map_locations[$location];

        $this->setUserData([
            'location' => $location,
            'map_location' => $map_location,
        ], $user_id);
    }
}
