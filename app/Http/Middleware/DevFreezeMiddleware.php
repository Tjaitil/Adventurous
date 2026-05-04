<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class DevFreezeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Only active in local environment
        if (! app()->environment('local')) {
            return $next($request);
        }

        // Dev-admin routes are always exempt so the panel stays usable while frozen
        if ($request->is('dev/admin/*')) {
            return $next($request);
        }

        if (! Cache::get('devtools.frozen', false)) {
            return $next($request);
        }

        DB::beginTransaction();

        try {
            $response = $next($request);
        } finally {
            DB::rollBack();
        }

        return $response;
    }
}
