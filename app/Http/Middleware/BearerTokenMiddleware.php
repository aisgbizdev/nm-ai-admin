<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BearerTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $auth = (string) $request->header('Authorization', '');

        // Expect: "Bearer <token>"
        if (! preg_match('/^Bearer\s+(.+)$/i', trim($auth), $m)) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized: missing Bearer token',
            ], 401);
        }

        $token = trim($m[1]);
        if ($token === '') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized: empty token',
            ], 401);
        }

        $raw = (string) env('API_BEARER_TOKENS', '');
        $allowed = array_values(array_filter(array_map('trim', explode(',', $raw))));

        // Constant-time compare
        $ok = false;
        foreach ($allowed as $t) {
            if ($t !== '' && hash_equals($t, $token)) {
                $ok = true;
                break;
            }
        }

        if (! $ok) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized: invalid token',
            ], 401);
        }

        return $next($request);
    }
}
