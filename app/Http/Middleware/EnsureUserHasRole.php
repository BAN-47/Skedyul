<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Stop here with 403 unless the signed-in account has the role required by this route.
        abort_unless($request->user()?->usr_role === $role, 403, 'Unauthorized action.');

        return $next($request);
    }
}
