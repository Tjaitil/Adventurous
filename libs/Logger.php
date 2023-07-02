<?php

namespace App\libs;

use DateTime;
use Exception;

class Logger
{

    private static $logfile = 'adventurous.log';
    private static function getTimestamp()
    {
        return date("Y-m-d H:i:s", date_timestamp_get(new DateTime()));
    }

    public static function log($error)
    {
        $date = self::getTimestamp();
        $file = \fopen(\ROUTE_ROOT . self::$logfile, "a");
        if (\is_array($error)) {
            $error = \print_r($error, true);
        } else if ($error instanceof Exception) {
            \fwrite($file, "[{$date}] " . $error->getMessage() . "\n");
            \fwrite($file, "" . $error->getTraceAsString() . "\n");
        }
        \fwrite($file, "[{$date}] " . $error . "\n");
        \fclose($file);
    }
}
