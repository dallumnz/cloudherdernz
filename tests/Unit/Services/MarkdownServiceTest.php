<?php

use App\Services\MarkdownService;

beforeEach(function () {
    $this->service = new MarkdownService;
});

describe('MarkdownService', function () {
    it('converts basic markdown to HTML', function () {
        $markdown = '# Hello World';
        $html = $this->service->toHtml($markdown);

        expect($html)->toContain('<h1>Hello World</h1>');
    });

    it('converts paragraphs correctly', function () {
        $markdown = "This is paragraph one.\n\nThis is paragraph two.";
        $html = $this->service->toHtml($markdown);

        expect($html)->toContain('<p>This is paragraph one.</p>');
        expect($html)->toContain('<p>This is paragraph two.</p>');
    });

    it('converts bold and italic text', function () {
        $markdown = '**bold** and *italic*';
        $html = $this->service->toHtml($markdown);

        expect($html)->toContain('<strong>bold</strong>');
        expect($html)->toContain('<em>italic</em>');
    });

    it('converts links correctly', function () {
        $markdown = '[Link text](https://example.com)';
        $html = $this->service->toHtml($markdown);

        expect($html)->toContain('<a href="https://example.com">Link text</a>');
    });

    it('converts lists correctly', function () {
        $markdown = "- Item 1\n- Item 2\n- Item 3";
        $html = $this->service->toHtml($markdown);

        expect($html)->toContain('<ul>');
        expect($html)->toContain('<li>Item 1</li>');
        expect($html)->toContain('<li>Item 2</li>');
        expect($html)->toContain('<li>Item 3</li>');
        expect($html)->toContain('</ul>');
    });

    it('converts ordered lists correctly', function () {
        $markdown = "1. First\n2. Second\n3. Third";
        $html = $this->service->toHtml($markdown);

        expect($html)->toContain('<ol>');
        expect($html)->toContain('<li>First</li>');
        expect($html)->toContain('<li>Second</li>');
        expect($html)->toContain('<li>Third</li>');
        expect($html)->toContain('</ol>');
    });

    it('converts code blocks correctly', function () {
        $markdown = "```php\necho 'Hello';\n```";
        $html = $this->service->toHtml($markdown);

        expect($html)->toContain('<pre>');
        expect($html)->toContain('<code');
        expect($html)->toContain("echo 'Hello';");
    });

    it('converts inline code correctly', function () {
        $markdown = 'Use the `echo` command';
        $html = $this->service->toHtml($markdown);

        expect($html)->toContain('<code>echo</code>');
    });

    it('converts tables with GFM', function () {
        $markdown = "| Header 1 | Header 2 |\n|----------|----------|\n| Cell 1   | Cell 2   |";
        $html = $this->service->toHtml($markdown);

        expect($html)->toContain('<table>');
        expect($html)->toContain('<th>Header 1</th>');
        expect($html)->toContain('<th>Header 2</th>');
        expect($html)->toContain('<td>Cell 1</td>');
        expect($html)->toContain('<td>Cell 2</td>');
    });

    it('converts strikethrough with GFM', function () {
        $markdown = '~~deleted~~';
        $html = $this->service->toHtml($markdown);

        expect($html)->toContain('<del>deleted</del>');
    });

    it('returns empty string for null input', function () {
        $html = $this->service->toHtml(null);

        expect($html)->toBe('');
    });

    it('returns empty string for empty string input', function () {
        $html = $this->service->toHtml('');

        expect($html)->toBe('');
    });

    it('escapes unsafe HTML by default', function () {
        $markdown = '<script>alert("xss")</script>Hello';
        $html = $this->service->toHtml($markdown);

        expect($html)->not->toContain('<script>');
        expect($html)->toContain('&lt;script&gt;');
        expect($html)->toContain('Hello');
    });

    it('caches rendered HTML', function () {
        $markdown = '# Cached Content';
        $cacheKey = 'test-cache-key';

        // First call should cache
        $html1 = $this->service->toHtmlCached($markdown, $cacheKey, 60);

        // Second call should return cached value
        $html2 = $this->service->toHtmlCached($markdown, $cacheKey, 60);

        expect($html1)->toBe($html2);
        expect(cache()->has("markdown:{$cacheKey}"))->toBeTrue();

        // Clear cache
        $this->service->clearCache($cacheKey);
        expect(cache()->has("markdown:{$cacheKey}"))->toBeFalse();
    });

    it('returns empty string for null input in cached method', function () {
        $html = $this->service->toHtmlCached(null, 'null-test', 60);

        expect($html)->toBe('');
    });
});
