<?php

return [
    // Embedding caching
    'embeddings' => [
        'cache' => (bool) env('AI_EMBEDDING_CACHE', false),
        'cache_ttl' => (int) env('AI_EMBEDDING_CACHE_TTL', 86400),
    ],

    // Vector store caching
    'vector_store' => [
        'cache_enabled' => (bool) env('AI_VECTOR_CACHE_ENABLED', false),
        'cache_ttl' => (int) env('AI_VECTOR_CACHE_TTL', 600),
    ],

    // API response caching
    'api' => [
        'cache_enabled' => (bool) env('CACHE_API_ENABLED', false),
        'cache_ttl' => (int) env('CACHE_API_TTL', 60),
        'search_ttl' => (int) env('CACHE_API_SEARCH_TTL', 30),
        'node_ttl' => (int) env('CACHE_API_NODE_TTL', 3600),
    ],
];
