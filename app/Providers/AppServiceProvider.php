<?php

namespace App\Providers;

use App\ValueObjects\GameLog;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * @param  array<int|string, mixed>  $data
         * @param  array<int, GameLog>  $logs
         * @param  int  $code
         * @param  array<int|string, mixed>  $headers
         * @param  int  $options
         * @return \Illuminate\Http\JsonResponse
         */
        Response::macro('jsonWithGameLogs',
            function ($data = [], array|GameLog $logs = [], $code = 200, $headers = [], $options = 0) {
                if ($logs instanceof GameLog) {
                    $logs = [$logs];
                }

                return response()->json([
                    ...$data,
                    'logs' => $logs,
                ], $code, $headers, $options);
            });
    }
}
