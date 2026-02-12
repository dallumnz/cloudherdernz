<?php

namespace App\Http\Middleware;

use App\Models\AnalyticsEvent;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureAnalytics
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Skip if the route name starts with 'admin.'
        if ($this->isAdminRoute($request)) {
            return $response;
        }

        // Capture analytics data
        $this->captureEvent($request);

        return $response;
    }

    /**
     * Check if the current route is an admin route.
     */
    private function isAdminRoute(Request $request): bool
    {
        $routeName = $request->route()?->getName();

        if ($routeName === null) {
            return false;
        }

        return str_starts_with($routeName, 'admin.');
    }

    /**
     * Capture the analytics event.
     */
    private function captureEvent(Request $request): void
    {
        AnalyticsEvent::create([
            'url' => $request->fullUrl(),
            'user_id' => $request->user()?->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent() ?? '',
            'referrer' => $request->headers->get('referer'),
            'created_at' => now(),
        ]);
    }
}
