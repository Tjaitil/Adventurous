<?php

class TerminalLogger
{
    private static $message;

    public function __construct()
    {
    }

    public static function log(string $message, string $type)
    {
        self::$message = $message;

        switch ($type) {
            case 'success':
                # code...
                self::success($message);
                break;
            case 'info':

                break;
            case 'warning':

                break;
            case 'error':
                self::error($message);
                break;
            default:
                break;
        }
    }

    static function success($message)
    {
        echo sprintf("\033[32m %s \n \033[0m", $message);
    }

    static function info($message)
    {
        echo sprintf("%s \n", $message);
    }

    static function warning($message)
    {
        echo sprintf("\033[33m %s \n \033[0m", $message);
    }

    static function error($message)
    {
        echo sprintf("\033[31m %s \n \033[0m", $message);
    }
}
