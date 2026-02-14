<?php

use App\Models\ImagePost;
use App\Models\Post;
use App\Models\Taxonomy;
use App\Models\TaxonomyTerm;

describe('RSS Feed', function () {
    it('returns a successful response for feed', function () {
        $response = $this->get(route('feed'));

        $response->assertStatus(200);
    });

    it('returns application/rss+xml content type', function () {
        $response = $this->get(route('feed'));

        $response->assertHeader('Content-Type', 'application/rss+xml');
    });

    it('returns valid RSS 2.0 XML structure', function () {
        $response = $this->get(route('feed'));
        $content = $response->getContent();

        expect($content)->toContain('<?xml version="1.0" encoding="UTF-8"?>');
        expect($content)->toContain('<rss version="2.0"');
        expect($content)->toContain('<channel>');
        expect($content)->toContain('</channel>');
        expect($content)->toContain('</rss>');
    });

    it('includes published posts in feed', function () {
        $imagePost = ImagePost::factory()->create();
        $post = Post::factory()->create([
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
            'status' => 'published',
            'published_at' => now()->subDay(),
            'title' => 'Test RSS Post Title',
        ]);

        $response = $this->get(route('feed'));
        $content = $response->getContent();

        expect($content)->toContain('Test RSS Post Title');
        expect($content)->toContain(route('posts.show', $post));
    });

    it('includes post categories from taxonomy terms', function () {
        $tagTaxonomy = Taxonomy::factory()->create([
            'type' => 'tag',
            'slug' => 'tags',
        ]);
        $tag = TaxonomyTerm::factory()->create([
            'taxonomy_id' => $tagTaxonomy->id,
            'name' => 'Test Tag',
        ]);

        $imagePost = ImagePost::factory()->create();
        $post = Post::factory()->create([
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $post->taxonomyTerms()->attach($tag);

        $response = $this->get(route('feed'));
        $content = $response->getContent();

        expect($content)->toContain('<category>Test Tag</category>');
    });

    it('limits feed to latest 20 published posts', function () {
        // Create 25 published posts
        for ($i = 0; $i < 25; $i++) {
            $imagePost = ImagePost::factory()->create();
            Post::factory()->create([
                'postable_type' => ImagePost::class,
                'postable_id' => $imagePost->id,
                'status' => 'published',
                'published_at' => now()->subDays($i),
                'title' => "Post {$i}",
            ]);
        }

        $response = $this->get(route('feed'));
        $content = $response->getContent();

        // Count the number of <item> elements
        $itemCount = substr_count($content, '<item>');
        expect($itemCount)->toBe(20);
    });

    it('orders posts by published_at descending', function () {
        $imagePost1 = ImagePost::factory()->create();
        $post1 = Post::factory()->create([
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost1->id,
            'status' => 'published',
            'published_at' => now()->subDays(2),
            'title' => 'Older Post',
        ]);

        $imagePost2 = ImagePost::factory()->create();
        $post2 = Post::factory()->create([
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost2->id,
            'status' => 'published',
            'published_at' => now()->subDay(),
            'title' => 'Newer Post',
        ]);

        $response = $this->get(route('feed'));
        $content = $response->getContent();

        // Newer post should appear before older post
        $newerPosition = strpos($content, 'Newer Post');
        $olderPosition = strpos($content, 'Older Post');
        expect($newerPosition)->toBeLessThan($olderPosition);
    });

    it('excludes unpublished posts', function () {
        $imagePost = ImagePost::factory()->create();
        Post::factory()->create([
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
            'status' => 'draft',
            'published_at' => null,
            'title' => 'Draft Post Should Not Appear',
        ]);

        $response = $this->get(route('feed'));
        $content = $response->getContent();

        expect($content)->not->toContain('Draft Post Should Not Appear');
    });

    it('excludes future published posts', function () {
        $imagePost = ImagePost::factory()->create();
        Post::factory()->create([
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
            'status' => 'published',
            'published_at' => now()->addDay(),
            'title' => 'Future Post Should Not Appear',
        ]);

        $response = $this->get(route('feed'));
        $content = $response->getContent();

        expect($content)->not->toContain('Future Post Should Not Appear');
    });

    it('is accessible without authentication', function () {
        $response = $this->get(route('feed'));

        $response->assertStatus(200);
    });

    it('includes channel metadata', function () {
        $response = $this->get(route('feed'));
        $content = $response->getContent();

        expect($content)->toContain('<title>');
        expect($content)->toContain('<link>');
        expect($content)->toContain('<description>');
        expect($content)->toContain('<language>en</language>');
        expect($content)->toContain('<lastBuildDate>');
    });

    it('includes atom self-reference link', function () {
        $response = $this->get(route('feed'));
        $content = $response->getContent();

        expect($content)->toContain('xmlns:atom="http://www.w3.org/2005/Atom"');
        expect($content)->toContain('atom:link');
        expect($content)->toContain('rel="self"');
        expect($content)->toContain('type="application/rss+xml"');
    });

    it('uses CDATA for description content', function () {
        $imagePost = ImagePost::factory()->create();
        Post::factory()->create([
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
            'status' => 'published',
            'published_at' => now()->subDay(),
            'content' => '<p>HTML content with tags</p>',
        ]);

        $response = $this->get(route('feed'));
        $content = $response->getContent();

        expect($content)->toContain('<![CDATA[');
        expect($content)->toContain(']]>');
    });
});
