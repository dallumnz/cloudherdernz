<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Cache Service
 *
 * Provides centralized caching functionality for AI embeddings
 * and vector search results with namespaced cache keys and
 * configuration-driven TTL values.
 */
class CacheService
{
    /** @var string Cache key namespace for embeddings */
    protected const EMBEDDING_NAMESPACE = 'ai:embeddings:';

    /** @var string Cache key namespace for vector search */
    protected const SEARCH_NAMESPACE = 'ai:search:';

    /** @var string Cache key for tracking embedding keys */
    protected const EMBEDDING_KEYS_TRACKER = 'ai:embeddings:keys';

    protected bool $enabled;

    protected int $ttl;

    public function __construct()
    {
        $this->enabled = config('ai.embeddings.cache', false);
        $this->ttl = config('ai.embeddings.cache_ttl', 86400);
    }

    /**
     * Generate a namespaced cache key for embedding.
     *
     * @param  string  $text  The text to generate cache key for
     * @return string The namespaced cache key
     */
    public function getEmbeddingCacheKey(string $text): string
    {
        return self::EMBEDDING_NAMESPACE.hash('sha256', $text);
    }

    /**
     * Get cached embedding or null if not found/disabled.
     *
     * @param  string  $text  The text to look up
     * @return array<int, float>|null The cached embedding vector or null
     */
    public function getEmbedding(string $text): ?array
    {
        if (! $this->enabled) {
            return null;
        }

        $key = $this->getEmbeddingCacheKey($text);

        return Cache::get($key);
    }

    /**
     * Cache an embedding vector.
     *
     * @param  string  $text  The source text
     * @param  array<int, float>  $embedding  The embedding vector to cache
     */
    public function setEmbedding(string $text, array $embedding): void
    {
        if (! $this->enabled) {
            return;
        }

        $key = $this->getEmbeddingCacheKey($text);

        Cache::put($key, $embedding, $this->ttl);
        $this->trackEmbeddingKey($key);

        Log::debug('Embedding cached', ['key' => $key, 'ttl' => $this->ttl, 'namespace' => self::EMBEDDING_NAMESPACE]);
    }

    /**
     * Clear a specific embedding cache.
     *
     * @param  string  $text  The text whose embedding should be cleared
     * @return bool True if the key was found and removed
     */
    public function clearEmbedding(string $text): bool
    {
        $key = $this->getEmbeddingCacheKey($text);
        $this->untrackEmbeddingKey($key);

        return Cache::forget($key);
    }

    /**
     * Clear all embedding caches.
     *
     * @return int Number of cache keys cleared
     */
    public function clearAllEmbeddingCache(): int
    {
        $keys = Cache::get(self::EMBEDDING_KEYS_TRACKER, []);

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        Cache::forget(self::EMBEDDING_KEYS_TRACKER);

        Log::info('All embedding caches cleared', ['count' => count($keys)]);

        return count($keys);
    }

    /**
     * Generate a namespaced cache key for vector search.
     *
     * @param  string  $query  The search query
     * @param  int  $limit  Maximum number of results
     * @param  float  $minSimilarity  Minimum similarity threshold
     * @return string The namespaced cache key
     */
    public function getSearchCacheKey(string $query, int $limit = 10, float $minSimilarity = 0.0): string
    {
        $params = json_encode(compact('query', 'limit', 'minSimilarity'));

        return self::SEARCH_NAMESPACE.hash('sha256', $params);
    }

    /**
     * Get cached search results or null if not found/disabled.
     *
     * @param  string  $query  The search query
     * @param  int  $limit  Maximum number of results
     * @param  float  $minSimilarity  Minimum similarity threshold
     * @return array<int, array<string, mixed>>|null The cached results or null
     */
    public function getSearchResults(string $query, int $limit = 10, float $minSimilarity = 0.0): ?array
    {
        if (! config('ai.vector_store.cache_enabled', false)) {
            return null;
        }

        $key = $this->getSearchCacheKey($query, $limit, $minSimilarity);

        return Cache::get($key);
    }

    /**
     * Cache search results.
     *
     * @param  string  $query  The search query
     * @param  array<int, array<string, mixed>>  $results  The search results to cache
     * @param  int  $limit  Maximum number of results
     * @param  float  $minSimilarity  Minimum similarity threshold
     */
    public function setSearchResults(string $query, array $results, int $limit = 10, float $minSimilarity = 0.0): void
    {
        if (! config('ai.vector_store.cache_enabled', false)) {
            return;
        }

        $key = $this->getSearchCacheKey($query, $limit, $minSimilarity);
        $ttl = config('ai.vector_store.cache_ttl', 600);

        Cache::put($key, $results, $ttl);

        Log::debug('Search results cached', ['key' => $key, 'ttl' => $ttl, 'namespace' => self::SEARCH_NAMESPACE]);
    }

    /**
     * Check if embedding caching is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Get embedding TTL in seconds.
     */
    public function getTtl(): int
    {
        return $this->ttl;
    }

    /**
     * Track an embedding key for bulk clearing.
     *
     * @param  string  $key  The cache key to track
     */
    protected function trackEmbeddingKey(string $key): void
    {
        $keys = Cache::get(self::EMBEDDING_KEYS_TRACKER, []);
        $keys[] = $key;
        Cache::put(self::EMBEDDING_KEYS_TRACKER, array_unique($keys), $this->ttl * 2);
    }

    /**
     * Untrack an embedding key.
     *
     * @param  string  $key  The cache key to untrack
     */
    protected function untrackEmbeddingKey(string $key): void
    {
        $keys = Cache::get(self::EMBEDDING_KEYS_TRACKER, []);
        $keys = array_diff($keys, [$key]);
        Cache::put(self::EMBEDDING_KEYS_TRACKER, $keys, $this->ttl * 2);
    }
}
