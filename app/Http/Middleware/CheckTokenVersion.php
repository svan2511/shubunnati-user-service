<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenVersion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $sessionTokenVersion = $request->header('X-Token-Version');
        if (isset($sessionTokenVersion) && $user->token_version != $sessionTokenVersion) {
            return response()->json(['message' => 'Token expired. Please login again.'], 401);
        }

        return $next($request);
    }
}
