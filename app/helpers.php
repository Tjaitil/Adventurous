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
        var_dump('advResponse helper called');
    }
}
