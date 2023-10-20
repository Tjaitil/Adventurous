<?php

namespace App\tests\support\DatabaseUtils;

use App\Enums\GameLocations;
use App\Enums\GameMaps;
use App\Models\UserData;
use App\tests\support\SessionTrait;

trait UserTrait
{
    use SessionTrait;

    /**
     * 
     * @param int $user_id 
     * @param array $user_data 
     * @return void 
     */
    public function setUserData(array $user_data, ?int $user_id)
    {
        $user_id = $user_id ?? self::$user_id;
        UserData::where('id', $user_id)->update($user_data);
    }

    /**
     * 
     * @param string $location 
     * @param null|int $user_id 
     * @return void 
     */
    public function setUserCurrentLocation(string $location, ?int $user_id = null)
    {
        $map_locations = \array_flip(GameMaps::locationMapping());

        $map_location = $map_locations[$location];

        $this->setUserData([
            'location' => $location,
            'map_location' => $map_location
        ], $user_id);
    }
}
