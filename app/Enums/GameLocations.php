<?php

namespace App\Enums;

enum GameLocations: string
{
    case HIRTAM_LOCATION = 'hirtam';
    case PVITUL_LOCATION = 'pvitul';
    case KHANZ_LOCATION = 'khanz';
    case TER_LOCATION = 'ter';
    case FANSALPLAINS_LOCATION = 'fansalplains';
    case KRASNUR_LOCATION = 'krasnur';
    case TOWHAR_LOCATION = 'towhar';
    case GOLBAK_LOCATION = 'golbak';
    case FAGNA_LOCATION = 'fagna';
    case CRUENDO_LOCATION = 'cruendo';
    case SNERPIIR_LOCATION = 'snerpiir';
    case TASNOBIL_LOCATION = 'tasnobil';

    /**
     * @return string[]
     */
    public static function getDiplomacyLocations()
    {
        return [
            self::HIRTAM_LOCATION->value,
            self::PVITUL_LOCATION->value,
            self::KHANZ_LOCATION->value,
            self::TER_LOCATION->value,
            self::FANSALPLAINS_LOCATION->value,
        ];
    }

    /**
     * @return string[]
     */
    public static function getWarriorLocations()
    {
        return [
            self::KRASNUR_LOCATION->value,
            self::TASNOBIL_LOCATION->value,
        ];
    }

    /**
     * @return string[]
     */
    public static function getCropLocations()
    {
        return [
            self::KRASNUR_LOCATION->value,
            self::TOWHAR_LOCATION->value,
        ];
    }

    /**
     * @return string[]
     */
    public static function getMineLocations()
    {
        return [
            self::GOLBAK_LOCATION->value,
            self::SNERPIIR_LOCATION->value,
        ];
    }

    public static function values(): array
    {
        return [
            self::HIRTAM_LOCATION->value,
            self::PVITUL_LOCATION->value,
            self::KHANZ_LOCATION->value,
            self::TER_LOCATION->value,
            self::FANSALPLAINS_LOCATION->value,
            self::KRASNUR_LOCATION->value,
            self::TOWHAR_LOCATION->value,
            self::GOLBAK_LOCATION->value,
            self::FAGNA_LOCATION->value,
            self::CRUENDO_LOCATION->value,
            self::SNERPIIR_LOCATION->value,
            self::TASNOBIL_LOCATION->value,
        ];
    }
}
