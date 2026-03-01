<?php

use App\Models\Post;
use Illuminate\Support\Facades\Cache;

describe('Post Markdown HTML Rendering', function (): void {
    beforeEach(function (): void {
        Cache::flush();
    });

    it('renders markdown content as HTML', function (): void {
        $post = Post::factory()->create([
            'content' => "# Hello World\n\nThis is **bold** and *italic* text.",
        ]);

        $html = $post->content_html;

        expect($html)->toContain('<h1>Hello World</h1>');
        expect($html)->toContain('<strong>bold</strong>');
        expect($html)->toContain('<em>italic</em>');
    });

    it('returns null for empty content', function (): void {
        $post = Post::factory()->create([
            'content' => null,
        ]);

        expect($post->content_html)->toBeNull();
    });

    it('returns null for empty string content', function (): void {
        $post = Post::factory()->create([
            'content' => '',
        ]);

        expect($post->content_html)->toBeNull();
    });

    it('caches the rendered HTML', function (): void {
        $post = Post::factory()->create([
            'content' => '# Cached Content',
        ]);

        // First access - should cache
        $html1 = $post->content_html;
        $cacheKey = "post:{$post->id}:content_html";

        expect(Cache::has($cacheKey))->toBeTrue();

        // Second access - should come from cache
        $html2 = $post->content_html;

        expect($html1)->toBe($html2);
    });

    it('clears cache when post is updated', function (): void {
        $post = Post::factory()->create([
            'content' => '# Original',
        ]);

        // Access to cache
        $post->content_html;
        $cacheKey = "post:{$post->id}:content_html";

        expect(Cache::has($cacheKey))->toBeTrue();

        // Update the post
        $post->update(['content' => '# Updated']);

        expect(Cache::has($cacheKey))->toBeFalse();
    });

    it('clears cache when post is deleted', function (): void {
        $post = Post::factory()->create([
            'content' => '# To Delete',
        ]);

        // Access to cache
        $post->content_html;
        $cacheKey = "post:{$post->id}:content_html";

        expect(Cache::has($cacheKey))->toBeTrue();

        // Delete the post
        $post->delete();

        expect(Cache::has($cacheKey))->toBeFalse();
    });

    it('renders excerpt as HTML', function (): void {
        $post = Post::factory()->create([
            'excerpt' => '**Bold** excerpt text',
        ]);

        $html = $post->excerpt_html;

        expect($html)->toContain('<strong>Bold</strong>');
    });

    it('returns null for empty excerpt', function (): void {
        $post = Post::factory()->create([
            'excerpt' => null,
        ]);

        expect($post->excerpt_html)->toBeNull();
    });

    it('strips unsafe HTML from markdown', function (): void {
        $post = Post::factory()->create([
            'content' => '# Test\n\n<script>alert("xss")</script>',
        ]);

        $html = $post->content_html;

        expect($html)->not->toContain('<script>');
        expect($html)->not->toContain('</script>');
        // The text content inside script tags is preserved when stripping HTML
        expect($html)->toContain('alert');
    });

    it('handles complex markdown structures', function (): void {
        $content = <<<'MD'
# Main Heading

## Sub Heading

- List item 1
- List item 2
- List item 3

1. Ordered item 1
2. Ordered item 2

> This is a blockquote

```php
echo "Hello World";
```

[Link text](https://example.com)
MD;

        $post = Post::factory()->create([
            'content' => $content,
        ]);

        $html = $post->content_html;

        expect($html)->toContain('<h1>Main Heading</h1>');
        expect($html)->toContain('<h2>Sub Heading</h2>');
        expect($html)->toContain('<ul>');
        expect($html)->toContain('<ol>');
        expect($html)->toContain('<blockquote>');
        expect($html)->toContain('<pre><code');
        expect($html)->toContain('<a href="https://example.com">Link text</a>');
    });

    it('handles newsletter post markdown', function (): void {
        $post = Post::factory()->create([
            'content' => <<<'MD'
# Newsletter Title

Dear subscribers,

Here is this week's update:

## Featured Content

- **Article 1**: Description here
- **Article 2**: Another description

Best regards,
The Team
MD,
        ]);

        $html = $post->content_html;

        expect($html)->toContain('<h1>Newsletter Title</h1>');
        expect($html)->toContain('<h2>Featured Content</h2>');
        expect($html)->toContain('<strong>Article 1</strong>');
    });
});
