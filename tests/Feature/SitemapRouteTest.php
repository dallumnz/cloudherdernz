<?php

use App\Models\ImagePost;
use App\Models\Post;
use App\Models\Taxonomy;
use App\Models\TaxonomyTerm;

describe('Sitemap Route', function () {
    it('returns a successful response for sitemap.xml', function () {
        $response = $this->get(route('sitemap'));

        $response->assertStatus(200);
    });

    it('returns application/xml content type', function () {
        $response = $this->get(route('sitemap'));

        $response->assertHeader('Content-Type', 'application/xml');
    });

    it('returns valid XML structure', function () {
        $response = $this->get(route('sitemap'));
        $content = $response->getContent();

        expect($content)->toContain('<?xml version="1.0" encoding="UTF-8"?>');
        expect($content)->toContain('xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"');
        expect($content)->toContain('<urlset');
        expect($content)->toContain('</urlset>');
    });

    it('includes published post URLs in sitemap', function () {
        // Create a published post
        $imagePost = ImagePost::factory()->create();
        $post = Post::factory()->create([
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get(route('sitemap'));
        $content = $response->getContent();

        $expectedUrl = route('posts.show', $post);
        expect($content)->toContain($expectedUrl);
    });

    it('includes category URLs in sitemap', function () {
        // Create category taxonomy and term
        $categoryTaxonomy = Taxonomy::factory()->create([
            'type' => 'category',
            'slug' => 'categories',
        ]);
        $category = TaxonomyTerm::factory()->create([
            'taxonomy_id' => $categoryTaxonomy->id,
            'slug' => 'test-category',
        ]);

        $response = $this->get(route('sitemap'));
        $content = $response->getContent();

        $expectedUrl = route('categories.show', $category);
        expect($content)->toContain($expectedUrl);
    });

    it('includes tag URLs in sitemap', function () {
        // Create tag taxonomy and term
        $tagTaxonomy = Taxonomy::factory()->create([
            'type' => 'tag',
            'slug' => 'tags',
        ]);
        $tag = TaxonomyTerm::factory()->create([
            'taxonomy_id' => $tagTaxonomy->id,
            'slug' => 'test-tag',
        ]);

        $response = $this->get(route('sitemap'));
        $content = $response->getContent();

        $expectedUrl = route('tags.show', $tag);
        expect($content)->toContain($expectedUrl);
    });

    it('returns cached sitemap on subsequent requests', function () {
        // First request
        $response1 = $this->get(route('sitemap'));
        $content1 = $response1->getContent();

        // Create new content (should not appear in cached response)
        $imagePost = ImagePost::factory()->create();
        Post::factory()->create([
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
            'status' => 'published',
            'published_at' => now()->subDay(),
            'slug' => 'new-post',
        ]);

        // Second request should return cached content
        $response2 = $this->get(route('sitemap'));
        $content2 = $response2->getContent();

        expect($content1)->toBe($content2);
    });

    it('sitemap is accessible without authentication', function () {
        $response = $this->get(route('sitemap'));

        $response->assertStatus(200);
    });
});
