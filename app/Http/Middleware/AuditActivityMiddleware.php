<?php

namespace App\Http\Middleware;

use App\Services\AuditActivityLogger;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuditActivityMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $response = $next($request);

        if (!$user || !in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return $response;
        }

        $route = $request->route();
        $target = $route?->getName() ?: trim($request->path(), '/');
        $targetId = null;
        foreach ($route?->parameters() ?? [] as $parameter) {
            $candidate = is_object($parameter) && method_exists($parameter, 'getRouteKey')
                ? (string) $parameter->getRouteKey()
                : (string) $parameter;
            if (Str::isUuid($candidate)) {
                $targetId = $candidate;
                break;
            }
        }
        $action = match ($request->method()) {
            'POST' => 'Created / Submitted',
            'PUT', 'PATCH' => 'Updated',
            'DELETE' => 'Deleted',
        };
        $status = $response->getStatusCode();
        $role = Str::headline($user->usr_role ?? 'user');
        $description = "Role: {$role}; Route: {$target}; HTTP {$status}";

        AuditActivityLogger::record(
            $user,
            $status >= 400 ? "Failed {$action}" : $action,
            $target,
            $description,
            $request->ip(),
            $targetId
        );

        return $response;
    }
}
