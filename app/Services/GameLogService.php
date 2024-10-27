<?php

namespace App\Services;

use App\Enums\GameLogTypes;
use App\ValueObjects\GameLog;

final class GameLogService
{
    private static string $adapter = 'session';

    public function __construct() {}

    public static function log(GameLog $gameLog): void
    {
        if (self::$adapter === 'session') {
            self::logToSession($gameLog);
        }
    }

    /**
     * Should at some point be extracted
     */
    public static function logToSession(GameLog $gameLog): void
    {
        $log = session()->get('log', []);
        if ($log > 500) {
            array_shift($log);
        }

        array_push($log, $gameLog->toArray());

        session()->put('log', $log);

    }

    public static function addLog(string $message, GameLogTypes $gameLogType): GameLog
    {
        $log = new GameLog($message, $gameLogType);

        self::log($log);

        return $log;
    }

    public static function addSuccessLog(string $message): GameLog
    {
        return self::addLog($message, GameLogTypes::SUCCESS);
    }

    public static function addInfoLog(string $message): GameLog
    {
        return self::addLog($message, GameLogTypes::INFO);
    }

    public static function addWarningLog(string $message): GameLog
    {
        return self::addLog($message, GameLogTypes::WARNING);
    }

    public static function addErrorLog(string $message): GameLog
    {
        return self::addLog($message, GameLogTypes::ERROR);
    }
}
