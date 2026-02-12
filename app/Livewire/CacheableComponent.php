<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait CacheableComponent
{
    /**
     * Get cached data or compute and cache it.
     *
     * @param  string  $key  Cache key suffix
     * @param  callable  $callback  Function to compute data
     * @param  int  $ttl  Cache TTL in seconds
     * @return mixed
     */
    protected function getCachedData(string $key, callable $callback, int $ttl = 60)
    {
        $cacheKey = $this->componentCacheKey($key);

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $data = $callback();

        Cache::put($cacheKey, $data, $ttl);

        Log::debug('Component data cached', ['key' => $cacheKey, 'ttl' => $ttl]);

        return $data;
    }

    /**
     * Get cached HTML or compute and cache it.
     *
     * @param  string  $key  Cache key suffix
     * @param  callable  $callback  Function that returns HTML string
     * @param  int  $ttl  Cache TTL in seconds
     * @return string
     */
    protected function getCachedHtml(string $key, callable $callback, int $ttl = 60): string
    {
        $cacheKey = $this->componentCacheKey('html:'.$key);

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $html = $callback();

        Cache::put($cacheKey, $html, $ttl);

        return $html;
    }

    /**
     * Generate a unique cache key for this component.
     *
     * @param  string  $suffix  Additional suffix for key
     * @return string
     */
    protected function componentCacheKey(string $suffix = ''): string
    {
        $base = static::class.':'.$this->getId();

        return $suffix ? $base.':'.$suffix : $base;
    }

    /**
     * Generate user-specific cache key.
     *
     * @param  string  $suffix  Additional suffix
     * @return string
     */
    protected function userCacheKey(string $suffix = ''): string
    {
        $userId = auth()->id() ?? 'guest';

        return $this->componentCacheKey('user:'.$userId.':'.$suffix);
    }

    /**
     * Clear specific component cache.
     *
     * @param  string  $key  Cache key suffix
     * @return bool
     */
    protected function clearComponentCache(string $key): bool
    {
        return Cache::forget($this->componentCacheKey($key));
    }

    /**
     * Clear all caches for this component.
     *
     * @return int
     */
    protected function clearAllComponentCaches(): int
    {
        $pattern = static::class.':'.$this->getId().':*';
        $keys = Cache::get($pattern, []);

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        return count($keys);
    }
}
