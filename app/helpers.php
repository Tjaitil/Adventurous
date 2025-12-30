<?php

use App\Http\Responses\AdvResponse;
use Symfony\Component\HttpFoundation\Response;

if (! function_exists('advResponse')) {
    /**
     * @param  array<string, mixed>  $data  Data key of the response
     * @param  int  $status
     * @param  array<string, mixed>  $headers
     * @return \App\Http\Responses\AdvResponse
     */
    function advResponse($data = [], $status = Response::HTTP_OK, $headers = [])
    {
        return new AdvResponse($data, $status, $headers);
    }
}

if (! function_exists('auth_user')) {
    /**
     * @return \App\Models\User|null
     */
    function auth_user()
    {
        $user = Auth::user();
        if ($user instanceof \App\Models\User === false) {
            return null;
        }

        return $user;
    }
}
