<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanctumGuest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->bearerToken()) {
            return response()->json([
                'message' => 'You are already logged in. Please logout first to register a new account.',
                'error' => 'Already authenticated'
            ], 403);
        }

        return $next($request);
    }
}
