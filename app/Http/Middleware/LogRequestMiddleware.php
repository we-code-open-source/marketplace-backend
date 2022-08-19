<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

/**
 * This middeware to log requests , it is focus on log errors (respones with status code >= 400)
 */
class LogRequestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($response->status() >= 400) {
            $log = [
                'URI' => $request->getUri(),
                'METHOD' => $request->getMethod(),
                'RESPONSE_STATUS' => $response->status(),
                'REQUEST_BODY' => $request->all(),
                'RESPONSE' => $response->getContent(),
                "HEADER"  => $request->header(),
            ];

            Log::channel('requests')->info(json_encode($log));
        }

        return $response;
    }
}
