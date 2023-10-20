<?php

namespace App\Enums;

enum GameMaps: string
{
    case _1_3 = "1.3";
    case _2_2 = "2.2";
    case _2_3 = "2.3";
    case _2_5 = "2.5";
    case _2_6 = "2.6";
    case _2_9 = "2.9";
    case _2_10 = "2.10";
    case _3_2 = "3.2";
    case _3_3 = "3.3";
    case _3_4 = "3.4";
    case _3_5 = "3.5";
    case _3_6 = "3.6";
    case _3_8 = "3.8";
    case _3_9 = "3.9";
    case _3_10 = "3.10";
    case _4_2 = "4.2";
    case _4_3 = "4.3";
    case _4_4 = "4.4";
    case _4_5 = "4.5";
    case _4_6 = "4.6";
    case _4_7 = "4.7";
    case _4_9 = "4.9";
    case _5_2 = "5.2";
    case _5_3 = "5.3";
    case _5_4 = "5.4";
    case _5_5 = "5.5";
    case _5_6 = "5.6";
    case _5_7 = "5.7";
    case _6_2 = "6.2";
    case _6_3 = "6.3";
    case _6_4 = "6.4";
    case _6_5 = "6.5";
    case _6_6 = "6.6";
    case _6_7 = "6.7";
    case _7_2 = "7.2";
    case _7_3 = "7.3";
    case _7_4 = "7.4";
    case _7_5 = "7.5";
    case _7_6 = "7.6";
    case _8_2 = "8.2";
    case _8_3 = "8.3";
    case _8_4 = "8.4";
    case _9_9 = "9.9";



    /**
     * 
     * @return array 
     */
    public static function getMaps()
    {
        return \array_column(self::cases(), 'value');
    }



    /**
     * 
     * @return \App\Enums\GameLocations[] 
     */
    public static function locationMapping()
    {
        return [
            self::_2_6->value => GameLocations::TASNOBIL_LOCATION->value,
            self::_2_9->value => GameLocations::PVITUL_LOCATION->value,
            self::_3_5->value => GameLocations::GOLBAK_LOCATION->value,
            self::_3_6->value => GameLocations::KRASNUR_LOCATION->value,
            self::_4_3->value => GameLocations::FANSALPLAINS_LOCATION->value,
            self::_4_9->value => GameLocations::HIRTAM_LOCATION->value,
            self::_5_5->value => GameLocations::SNERPIIR_LOCATION->value,
            self::_5_7->value => GameLocations::TOWHAR_LOCATION->value,
            self::_6_6->value => GameLocations::CRUENDO_LOCATION->value,
            self::_6_3->value => GameLocations::TER_LOCATION->value,
            self::_7_5->value => GameLocations::FAGNA_LOCATION->value,
            self::_8_2->value => GameLocations::KHANZ_LOCATION->value,
        ];
    }
}
