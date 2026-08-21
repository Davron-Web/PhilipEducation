<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Access denied');
        }

        // "admin|superadmin" → ['admin', 'superadmin']
        $allowed = array_map('trim', explode('|', $roles));

        if (! in_array($user->role?->name, $allowed, true)) {
            abort(403, 'Access denied');
        }

        return $next($request);
    }
}
