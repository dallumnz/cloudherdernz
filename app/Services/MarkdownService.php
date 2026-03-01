<?php

namespace App\Services;

use League\CommonMark\GithubFlavoredMarkdownConverter;

/**
 * Markdown Service
 *
 * Converts Markdown content to HTML using league/commonmark.
 * Supports GitHub Flavored Markdown (GFM) for enhanced features
 * like tables, strikethrough, and task lists.
 */
class MarkdownService
{
    private GithubFlavoredMarkdownConverter $converter;

    /**
     * Create a new MarkdownService instance.
     */
    public function __construct()
    {
        $this->converter = new GithubFlavoredMarkdownConverter([
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
        ]);
    }

    /**
     * Convert Markdown to HTML.
     *
     * @param  string|null  $markdown  The markdown content to convert
     * @return string The rendered HTML
     */
    public function toHtml(?string $markdown): string
    {
        if (blank($markdown)) {
            return '';
        }

        return $this->converter->convert($markdown)->getContent();
    }

    /**
     * Convert Markdown to HTML with a cached result.
     * This is useful for frequently accessed content.
     *
     * @param  string|null  $markdown  The markdown content to convert
     * @param  string  $cacheKey  Unique key for caching
     * @param  int  $seconds  Cache duration in seconds
     * @return string The rendered HTML
     */
    public function toHtmlCached(?string $markdown, string $cacheKey, int $seconds = 3600): string
    {
        if (blank($markdown)) {
            return '';
        }

        return cache()->remember("markdown:{$cacheKey}", $seconds, function () use ($markdown) {
            return $this->toHtml($markdown);
        });
    }

    /**
     * Clear the cached HTML for a given key.
     *
     * @param  string  $cacheKey  The cache key to clear
     */
    public function clearCache(string $cacheKey): void
    {
        cache()->forget("markdown:{$cacheKey}");
    }
}
