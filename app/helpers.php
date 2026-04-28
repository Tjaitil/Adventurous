<?php

use App\Http\Responses\AdvResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

if (! function_exists('advResponse')) {
    /**
     * @param  array<string, mixed>  $data  Data key of the response
     * @param  int  $status
     * @param  array<string, mixed>  $headers
     * @return AdvResponse
     */
    function advResponse($data = [], $status = Response::HTTP_OK, $headers = [])
    {
        return new AdvResponse($data, $status, $headers);
    }
}

if (! function_exists('auth_user')) {
    /**
     * @return User|null
     */
    function auth_user()
    {
        $user = Auth::user();
        if ($user instanceof User === false) {
            return null;
        }

        return $user;
    }
}

if (! function_exists('show_dev_tools')) {
    function show_dev_tools(): bool
    {
        return Auth::user()->is_admin && app()->environment('local');
    }
}
