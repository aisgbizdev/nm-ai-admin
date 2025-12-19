<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan user login
        if (! auth()->check()) {
            abort(403, 'Unauthorized');
        }

        // Cek apakah role user ada di daftar role yang diizinkan
        if (! in_array(auth()->user()->role, $roles)) {
            abort(403, 'Access denied');
        }

        return $next($request);
    }
}
