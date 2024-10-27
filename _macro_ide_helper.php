<?php

/**
 * This file is for Laravel ide-helper. Could be removed if generation is figured out.
 * @see https://github.com/barryvdh/laravel-ide-helper/issues/1601
 */
namespace Illuminate\Contracts\Routing {
       interface ResponseFactory
       {
       /**
         * @param  array<int|string, mixed>  $data
         * @param  array<int, \App\ValueObjects\GameLog>|\App\ValueObjects\GameLog>  $messages
         * @param  int  $code
         * @param  array<int|string, mixed>  $headers
         * @param  int  $options
         * @return \Illuminate\Http\JsonResponse
         */
              public function jsonWithGameLogs($data = [], array|\App\ValueObjects\GameLog $messages = [], $code = 200, $headers = [], $options = 0);

       }
}