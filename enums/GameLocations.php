<?php

namespace App\enums;

enum GameLocations: string
{
    case HIRTAM_LOCATION = "hirtam";
    case PVITUL_LOCATION = "pvitul";
    case KHANZ_LOCATION = "khanz";
    case TER_LOCATION = "ter";
    case FANSALPLAINS_LOCATION = "fansalplains";
    case KRASNUR_LOCATION = "krasnur";
    case TOWHAR_LOCATION = "towhar";
    case GOLBAK_LOCATION = "golbak";
    case FAGNA_LOCATION = "fagna";


    /**
     * 
     * @return string[] 
     */
    public static function getDiplomacyLocations()
    {
        return [
            self::HIRTAM_LOCATION,
            self::PVITUL_LOCATION,
            self::KHANZ_LOCATION,
            self::TER_LOCATION,
            self::FANSALPLAINS_LOCATION
        ];
    }



    /**
     * 
     * @return string[]
     */
    public static function getWarriorLocations()
    {
        return [
            self::KRASNUR_LOCATION,
            self::TOWHAR_LOCATION,
        ];
    }



    /**
     * 
     * @return string[]
     */
    public static function getCropLocations()
    {
        return [
            self::KRASNUR_LOCATION,
            self::TOWHAR_LOCATION,
        ];
    }



    /**
     * 
     * @return string[]
     */
    public static function getMineLocations()
    {
        return [
            self::GOLBAK_LOCATION,
            self::FAGNA_LOCATION,
        ];
    }
}
