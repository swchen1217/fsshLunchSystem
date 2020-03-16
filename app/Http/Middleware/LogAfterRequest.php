<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Log;

class LogAfterRequest {

    public function handle($request, \Closure  $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        Log::channel('request')->info('app.requests', ['ip' => $request->ip(),'request' => $request->all(), 'response' => $response]);
    }

}
