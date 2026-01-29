<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OptionalAuth
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->bearerToken()) {
            // user() autentica davvero, check() no
            Auth::setUser(
                Auth::guard('sanctum')->user()
            );
        }
        
        return $next($request);
    }
}