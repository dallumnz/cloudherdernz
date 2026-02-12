<?php

use App\Models\ImagePost;
use App\Models\Post;
use App\Models\Taxonomy;
use App\Models\TaxonomyTerm;
use App\Services\SitemapGenerator;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->generator = new SitemapGenerator;
    Cache::flush();
});

describe('SitemapGenerator', function () {
    it('generates valid XML sitemap structure', function () {
        $xml = $this->generator->generate();

        // Check XML declaration
        expect($xml)->toContain('<?xml version="1.0" encoding="UTF-8"?>');

        // Check namespace
        expect($xml)->toContain('xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"');

        // Check root element
        expect($xml)->toContain('<urlset');
        expect($xml)->toContain('</urlset>');
    });

    it('includes published posts in sitemap', function () {
        // Create a published post
        $imagePost = ImagePost::factory()->create();
        $post = Post::factory()->create([
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
            'status' => 'published',
            'published_at' => now()->subDay(),
            'slug' => 'test-post',
        ]);

        $xml = $this->generator->generate();

        // Check post URL is included
        $expectedUrl = route('posts.show', $post);
        expect($xml)->toContain($expectedUrl);

        // Check post has correct changefreq and priority
        expect($xml)->toContain('<changefreq>daily</changefreq>');
        expect($xml)->toContain('<priority>0.8</priority>');
    });

    it('excludes unpublished posts from sitemap', function () {
        // Create a draft post
        $imagePost = ImagePost::factory()->create();
        $post = Post::factory()->create([
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
            'status' => 'draft',
            'slug' => 'draft-post',
        ]);

        $xml = $this->generator->generate();

        // Check post URL is NOT included
        $expectedUrl = route('posts.show', $post);
        expect($xml)->not->toContain($expectedUrl);
    });

    it('includes categories in sitemap', function () {
        // Create category taxonomy and term
        $categoryTaxonomy = Taxonomy::factory()->create([
            'type' => 'category',
            'slug' => 'categories',
        ]);
        $category = TaxonomyTerm::factory()->create([
            'taxonomy_id' => $categoryTaxonomy->id,
            'slug' => 'test-category',
        ]);

        $xml = $this->generator->generate();

        // Check category URL is included
        $expectedUrl = route('categories.show', $category);
        expect($xml)->toContain($expectedUrl);

        // Check category has correct changefreq and priority
        expect($xml)->toContain('<changefreq>weekly</changefreq>');
        expect($xml)->toContain('<priority>0.6</priority>');
    });

    it('includes tags in sitemap', function () {
        // Create tag taxonomy and term
        $tagTaxonomy = Taxonomy::factory()->create([
            'type' => 'tag',
            'slug' => 'tags',
        ]);
        $tag = TaxonomyTerm::factory()->create([
            'taxonomy_id' => $tagTaxonomy->id,
            'slug' => 'test-tag',
        ]);

        $xml = $this->generator->generate();

        // Check tag URL is included
        $expectedUrl = route('tags.show', $tag);
        expect($xml)->toContain($expectedUrl);

        // Check tag has correct changefreq and priority
        expect($xml)->toContain('<changefreq>weekly</changefreq>');
        expect($xml)->toContain('<priority>0.6</priority>');
    });

    it('includes lastmod dates in ISO8601 format', function () {
        $imagePost = ImagePost::factory()->create();
        $post = Post::factory()->create([
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
            'status' => 'published',
            'published_at' => now()->subDay(),
            'updated_at' => now(),
        ]);

        $xml = $this->generator->generate();

        // Check lastmod is present and in ISO8601 format
        expect($xml)->toContain('<lastmod>');
        expect($xml)->toContain($post->updated_at->toIso8601String());
    });

    it('caches the sitemap for 1 hour', function () {
        // First call should cache
        $xml1 = $this->generator->generate();

        // Create a new post (should not appear in cached version)
        $imagePost = ImagePost::factory()->create();
        Post::factory()->create([
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
            'status' => 'published',
            'published_at' => now()->subDay(),
            'slug' => 'new-post',
        ]);

        // Second call should return cached version (without new post)
        $xml2 = $this->generator->generate();

        expect($xml1)->toBe($xml2);
        expect(Cache::has('sitemap_xml'))->toBeTrue();
    });

    it('can clear the cache', function () {
        // Generate sitemap to populate cache
        $this->generator->generate();

        expect(Cache::has('sitemap_xml'))->toBeTrue();

        // Clear cache
        $this->generator->clearCache();

        expect(Cache::has('sitemap_xml'))->toBeFalse();
    });

    it('properly escapes XML special characters in URLs', function () {
        // Create content to generate URLs
        $imagePost = ImagePost::factory()->create();
        Post::factory()->create([
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $xml = $this->generator->generate();

        // The XML should not contain unescaped special characters
        // & should be &amp;, < should be &lt;, > should be &gt;
        expect($xml)->not->toMatch('/<loc>.*&[^amp;lt;gt;#].*<\/loc>/');
    });

    it('generates valid XML that can be parsed', function () {
        // Create some content
        $imagePost = ImagePost::factory()->create();
        Post::factory()->create([
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $xml = $this->generator->generate();

        // Try to parse the XML
        $dom = new DOMDocument;
        $isValid = $dom->loadXML($xml);

        expect($isValid)->toBeTrue();
    });
});
