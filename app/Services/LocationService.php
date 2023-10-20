<?php

namespace App\Services;

use App\Enums\GameLocations;

class LocationService
{
    public function __construct()
    {
    }



    /**
     * Check if provided location is a diplomacy location
     *
     * @param string $location
     * @return bool
     */
    public function isDiplomacyLocation($location)
    {
        return (in_array($location, GameLocations::getDiplomacyLocations()));
    }



    /**
     * Is user in a valid miner location
     *
     * @param string $location
     * @return bool
     */
    public function isMineLocation(string $location)
    {
        return (in_array($location, GameLocations::getMineLocations()));
    }


    /**
     * 
     * @param string $location 
     * @return bool 
     */
    public function isCropsLocation(string $location)
    {
        return (in_array($location, GameLocations::getCropLocations()));
    }
}
