<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiMonitoringMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);
        $response = $next($request);

        $duration = round((microtime(true) - $start) * 1000, 2);

        Log::channel('api')->info('API', [
            'method' => $request->method(),
            'endpoint' => '/'.$request->path(),
            'status' => $response->status(),
            'time_ms' => $duration,
            'ip' => $request->ip(),
            'user_id' => auth()->id(),
        ]);

        return $response;
    }
}
