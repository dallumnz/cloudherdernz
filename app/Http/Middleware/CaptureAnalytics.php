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
        // Skip analytics capture for admin routes
        if ($this->isAdminRoute($request)) {
            return $next($request);
        }

        // Process the request first
        $response = $next($request);

        // Capture analytics data
        $this->captureAnalytics($request);

        return $response;
    }

    /**
     * Check if the current route is an admin route.
     */
    protected function isAdminRoute(Request $request): bool
    {
        return str_starts_with($request->path(), 'admin');
    }

    /**
     * Capture analytics data from the request.
     */
    protected function captureAnalytics(Request $request): void
    {
        AnalyticsEvent::create([
            'user_id' => $request->user()?->id,
            'url' => $request->fullUrl(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('referer'),
        ]);
    }
}
