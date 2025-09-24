<?php

namespace App\Http\Middleware;

use App\Http\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Middleware to ensure authenticated user has one of the required roles.
 * Usage in routes: ->middleware('role:admin') or ->middleware('role:admin,member')
 */
class EnsureUserHasRole
{
    use ResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ?string $roles = null): mixed
    {
        $user = $request->user();

        if (!$user) {
            return $this->errorResponse(401, 'Unauthenticated');
        }

        // If no roles provided, deny by default
        if (!$roles) {
            return $this->errorResponse(403, 'Forbidden');
        }

        $allowed = array_map('trim', explode(',', $roles));

        if (!in_array($user->role, $allowed, true)) {
            return $this->errorResponse(403, 'Forbidden: Unauthorized permissions');
        }

        return $next($request);
    }
}
