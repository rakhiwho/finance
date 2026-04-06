<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
 

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * $roles can be a single role or multiple roles separated by '|'
     */
   public function handle(Request $request, Closure $next, $roles)
{
    if (!auth()->guard('sanctum')->check()) {   // Use sanctum guard explicitly
        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    $user = auth()->guard('sanctum')->user();

    $roleArray = explode('|', $roles);

    if (!in_array($user->role->name ?? '', $roleArray)) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    if (isset($user->is_active) && !$user->is_active) {
        return response()->json(['error' => 'User is inactive'], 403);
    }

    return $next($request);
}
}
