<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheApiResponses
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only cache GET requests
        if (! $request->isMethod('GET')) {
            return $next($request);
        }

        // Check if caching is enabled
        if (! config('ai.api.cache_enabled', false)) {
            return $next($request);
        }

        // Generate cache key from request
        $cacheKey = $this->generateCacheKey($request);

        // Check cache
        if (Cache::has($cacheKey)) {
            $cachedResponse = Cache::get($cacheKey);

            // Add cache headers to cached response
            return $this->addCacheHeaders($cachedResponse, true);
        }

        // Get response
        $response = $next($request);

        // Determine TTL based on route
        $ttl = $this->getTtl($request);

        // Cache the response
        Cache::put($cacheKey, $response, $ttl);

        // Add cache headers to response
        return $this->addCacheHeaders($response, false, $ttl);
    }

    /**
     * Add cache-related HTTP headers to the response.
     */
    protected function addCacheHeaders(Response $response, bool $isCached, ?int $ttl = null): Response
    {
        // Add X-Cache header to indicate cache status
        $response->headers->set('X-Cache', $isCached ? 'HIT' : 'MISS');

        // Add Cache-Control headers for client-side caching
        if ($ttl !== null) {
            $response->headers->set('Cache-Control', 'public, max-age='.$ttl);
            $response->headers->set('Expires', now()->addSeconds($ttl)->toRfc7231String());
        }

        return $response;
    }

    /**
     * Generate a unique cache key for the request.
     */
    protected function generateCacheKey(Request $request): string
    {
        $uri = $request->getUri();
        $query = $request->getQueryString() ?? '';

        return 'api:'.hash('sha256', $uri.$query);
    }

    /**
     * Get TTL based on route type.
     */
    protected function getTtl(Request $request): int
    {
        $uri = $request->getUri();

        if (str_contains($uri, '/api/search')) {
            return config('ai.api.search_ttl', 30);
        }

        if (preg_match('#/api/nodes/\d+#', $uri)) {
            return config('ai.api.node_ttl', 3600);
        }

        return config('ai.api.cache_ttl', 60);
    }
}
