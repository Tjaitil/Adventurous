<?php

use App\libs\DependencyContainer;
use App\libs\Logger;
use App\libs\Response;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Capsule\Manager as DB;

require __DIR__ . '/vendor/autoload.php';

require('root/routes.php');
require('root/config.php');
require('config/GameConstants.php');
require('tests/Logger/TerminalLogger.php');

$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $_SERVER["DB_SERVER"],
    'database' => $_SERVER["DB_NAME"],
    'username' => $_SERVER["DB_USERNAME"],
    'password' => $_SERVER["DB_PASS"],
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();

$capsule->bootEloquent();

$_SESSION['username'] = "tjaitil";
$_SESSION['gamedata']['location'] = "towhar";


$class = $argv[1];
$method = $argv[2] ?? "";
$rollback = $argv[3] ?? true;

try {

    // Start a transaction
    DB::beginTransaction();

    $class = DependencyContainer::getInstance()->get('App\\tests\\' . $class);

    $methods = get_class_methods($class);

    if ($method !== "" && $method !== "null") {
        $class->{$method}();
    } else {
        foreach (preg_grep('/^test_/', $methods) as $key => $method) {
            Response::clear();
            TerminalLogger::info('Calling ' . $method);
            $response = $class->{$method}();
            $message = "";
            if (is_null($response)) {
                TerminalLogger::warning('Not returning a Response');
            } else {
                $message .= $response->toJson() . ' ';
            }

            $message .= http_response_code();

            if (http_response_code() > 200) {
                TerminalLogger::error($message);
            } else {
                TerminalLogger::info($message);
                TerminalLogger::success($method . " passed");
            }
            TerminalLogger::info("\n");
        }
    }


    if (DB::transactionLevel() && filter_var($rollback, FILTER_VALIDATE_BOOLEAN)) {
        TerminalLogger::info('Rollback finished');
        DB::rollBack();
    } else if (DB::transactionLevel()) {
        TerminalLogger::info('Commited');
        DB::commit();
    }
} catch (Exception $e) {
    Logger::log($e);

    DB::rollBack();
    TerminalLogger::error($e->getMessage());
    TerminalLogger::error($e->getLine());
    TerminalLogger::error($e->getFile());
}
