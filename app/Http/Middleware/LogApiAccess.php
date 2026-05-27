<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogApiAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->getStatusCode() === 401) {
            $this->logFailed($request);
        } elseif ($request->user()) {
            $this->logSuccess($request);
        }

        return $response;
    }

    private function logSuccess(Request $request): void
    {
        $user = $request->user();

        activity()
            ->causedBy($user)
            ->withProperties([
                'ip' => $request->ip(),
                'method' => $request->method(),
                'route' => $request->route()?->getName() ?? $request->path(),
                'url' => $request->fullUrl(),
            ])
            ->event('api_access')
            ->log("API access by {$user->name} [{$request->method()}]");
    }

    private function logFailed(Request $request): void
    {
        $authHeader = $request->header('Authorization', 'none');
        $tokenHint = str_contains($authHeader, 'Bearer')
            ? 'Bearer ' . substr(explode(' ', $authHeader)[1] ?? '', 0, 8) . '...'
            : $authHeader;

        activity()
            ->withProperties([
                'ip' => $request->ip(),
                'method' => $request->method(),
                'route' => $request->route()?->getName() ?? $request->path(),
                'token_hint' => $tokenHint,
            ])
            ->event('api_auth_failed')
            ->log("Failed API authentication [{$request->method()}] {$request->path()}");
    }
}
