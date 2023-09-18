<?php

namespace App\services;

use App\enums\GameLocations;
use App\models\CityRelation;
use App\models\Diplomacy;

class DiplomacyService
{
    protected array $CurrentCityRelations = [];

    public function __construct(
        protected CityRelation $CityRelation,
        protected SessionService $sessionService,
        protected LocationService $locationService
    ) {
    }



    /**
     * Set new diplomacy for a location
     *
     * @param string $current_location
     * @param int $percentage
     *
     * @return void
     */
    public function setNewDiplomacy(string $current_location, int $percentage)
    {
        $this->CurrentCityRelations = CityRelation::where('city', $current_location);

        $Diplomacy = Diplomacy::where('username', $this->sessionService->getCurrentUsername())->get();

        for ($i = 0; $i < count(GameLocations::getDiplomacyLocations()); $i++) {
            $location = GameLocations::getDiplomacyLocations()[$i];

            $location_relation = floatval($this->CurrentCityRelations[$location]);

            // If the relation is 1 it will be unaffected
            if ($location_relation === 1) {
                continue;
            }

            $decimal = $percentage / 100;
            $percentage_relation = $location_relation * $decimal;

            if ($location_relation > 1) {
                $Diplomacy->{$location} += $percentage_relation;
            } else {
                $Diplomacy->{$location} -= $percentage_relation;
            }
        }
        $Diplomacy->save();
    }



    /**
     * Calculate new merchant price
     *
     * @param int $price
     * @param string $location
     *
     * @return float
     */
    public function calculateNewMerchantPrice($price, $location)
    {
        if (!$this->locationService->isDiplomacyLocation($location)) {
            return 0;
        }

        $diplomacy_price_adjust = 1;
        $location = str_replace("-", "", $location);

        $Diplomacy = Diplomacy::where('username', $this->sessionService->getCurrentUsername())->first();

        $diplomacy_price_ratio = floatval($Diplomacy->{$location});


        // Calculate price adjust. Subtract 1 which is middle point
        $diplomacy_price_adjust = abs(($diplomacy_price_ratio - 1) / 2);
        // If the ratio is more than 1.2, then the price will be adjusted by 10%
        if ($diplomacy_price_ratio > 1.2) {
            $diplomacy_price_adjust = 0.1;
        }

        $new_price = round(($diplomacy_price_ratio < 1) ?
            $price * (1.0 + $diplomacy_price_adjust) :
            $price * (1.0 - $diplomacy_price_adjust));

        return floor($new_price);
    }
}
