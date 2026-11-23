<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventBackHistory
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Tell browsers and proxies not to save this page for Back/Forward to reopen later.
        // The buttons still work, but the browser must request the page again; auth middleware
        // then checks the session, so a signed-out user is sent back to login.
        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate, private');
        // Older browsers use these headers too; together they reduce stale page restoration.
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
