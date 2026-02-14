<?php

use App\Models\ImagePost;
use App\Models\Post;
use App\Models\User;

describe('Search Feature', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
    });

    it('returns search results when query matches post title', function () {
        $imagePost = ImagePost::factory()->create();
        $post = Post::factory()->published()->create([
            'title' => 'Cloud Computing Basics',
            'content' => 'Some content about servers',
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
        ]);

        $response = $this->get(route('search.index', ['q' => 'Cloud']));

        $response->assertStatus(200);
        $response->assertViewIs('search.index');
        $response->assertViewHas('query', 'Cloud');
        $response->assertViewHas('posts');
        $response->assertSee($post->title);
    });

    it('returns search results when query matches post content', function () {
        $imagePost = ImagePost::factory()->create();
        $post = Post::factory()->published()->create([
            'title' => 'Server Management',
            'content' => 'Learn about cloud infrastructure and deployment',
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
        ]);

        $response = $this->get(route('search.index', ['q' => 'infrastructure']));

        $response->assertStatus(200);
        $response->assertViewIs('search.index');
        $response->assertSee($post->title);
    });

    it('returns empty results when no posts match query', function () {
        $imagePost = ImagePost::factory()->create();
        Post::factory()->published()->create([
            'title' => 'Different Topic',
            'content' => 'Some unrelated content',
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
        ]);

        $response = $this->get(route('search.index', ['q' => 'nonexistent']));

        $response->assertStatus(200);
        $response->assertViewIs('search.index');
        $response->assertSee('No results found');
        $response->assertSee('nonexistent');
    });

    it('only shows published posts in search results', function () {
        $imagePost = ImagePost::factory()->create();
        $publishedPost = Post::factory()->published()->create([
            'title' => 'Published Post About Clouds',
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
        ]);

        $draftImagePost = ImagePost::factory()->create();
        $draftPost = Post::factory()->draft()->create([
            'title' => 'Draft Post About Clouds',
            'postable_type' => ImagePost::class,
            'postable_id' => $draftImagePost->id,
        ]);

        $response = $this->get(route('search.index', ['q' => 'Clouds']));

        $response->assertStatus(200);
        $response->assertSee($publishedPost->title);
        $response->assertDontSee($draftPost->title);
    });

    it('validates search query is required', function () {
        $response = $this->get(route('search.index'));

        $response->assertSessionHasErrors(['q']);
    });

    it('validates search query minimum length', function () {
        $response = $this->get(route('search.index', ['q' => 'a']));

        $response->assertSessionHasErrors(['q']);
    });

    it('validates search query maximum length', function () {
        $response = $this->get(route('search.index', ['q' => str_repeat('a', 256)]));

        $response->assertSessionHasErrors(['q']);
    });

    it('paginates search results', function () {
        $imagePosts = ImagePost::factory()->count(15)->create();
        foreach ($imagePosts as $index => $imagePost) {
            Post::factory()->published()->create([
                'title' => "Cloud Post {$index}",
                'postable_type' => ImagePost::class,
                'postable_id' => $imagePost->id,
            ]);
        }

        $response = $this->get(route('search.index', ['q' => 'Cloud']));

        $response->assertStatus(200);
        $response->assertViewHas('posts', function ($posts) {
            return $posts->count() <= 12;
        });
    });

    it('displays search form on results page', function () {
        $response = $this->get(route('search.index', ['q' => 'test']));

        $response->assertStatus(200);
        $response->assertSee('Search posts...');
        $response->assertSee('value="test"', false);
    });

    it('handles special characters in search queries', function () {
        $imagePost = ImagePost::factory()->create();
        $post = Post::factory()->published()->create([
            'title' => 'Post with special chars: @#$%^&*()',
            'content' => 'Content here',
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
        ]);

        $response = $this->get(route('search.index', ['q' => '@#$%^&*()']));

        $response->assertStatus(200);
        $response->assertViewIs('search.index');
    });

    it('prevents sql injection attempts in search queries', function () {
        $sqlInjectionQueries = [
            "'; DROP TABLE posts; --",
            "' OR '1'='1",
            "'; DELETE FROM posts WHERE '1'='1",
            "1'; SELECT * FROM users; --",
        ];

        foreach ($sqlInjectionQueries as $query) {
            $response = $this->get(route('search.index', ['q' => $query]));

            // Should either return validation error or normal search results
            // but never execute the SQL injection
            $this->assertTrue(
                $response->status() === 200 || $response->status() === 302,
                "SQL injection query should not cause server error: {$query}"
            );
        }
    });

    it('only indexes published posts in scout search', function () {
        $imagePost = ImagePost::factory()->create();

        // Create a published post
        $publishedPost = Post::factory()->published()->create([
            'title' => 'Published Scout Test Post',
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
        ]);

        // Create a draft post
        $draftImagePost = ImagePost::factory()->create();
        $draftPost = Post::factory()->draft()->create([
            'title' => 'Draft Scout Test Post',
            'postable_type' => ImagePost::class,
            'postable_id' => $draftImagePost->id,
        ]);

        // Verify published post should be searchable
        expect($publishedPost->shouldBeSearchable())->toBeTrue();

        // Verify draft post should not be searchable
        expect($draftPost->shouldBeSearchable())->toBeFalse();
    });

    it('syncs post data to scout index correctly', function () {
        $imagePost = ImagePost::factory()->create();
        $post = Post::factory()->published()->create([
            'title' => 'Scout Sync Test',
            'content' => 'Test content for scout',
            'excerpt' => 'Test excerpt',
            'slug' => 'scout-sync-test',
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
        ]);

        $searchableArray = $post->toSearchableArray();

        expect($searchableArray)->toHaveKeys([
            'id',
            'title',
            'content',
            'excerpt',
            'slug',
            'status',
            'published_at',
        ]);

        expect($searchableArray['title'])->toBe('Scout Sync Test');
        expect($searchableArray['content'])->toBe('Test content for scout');
        expect($searchableArray['status'])->toBe('published');
    });

    it('rejects whitespace-only search queries', function () {
        $response = $this->get(route('search.index', ['q' => '   ']));

        $response->assertSessionHasErrors(['q']);
    });

    it('trims whitespace from search queries', function () {
        $imagePost = ImagePost::factory()->create();
        Post::factory()->published()->create([
            'title' => 'Cloud Computing',
            'content' => 'Content about cloud',
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
        ]);

        $response = $this->get(route('search.index', ['q' => '  Cloud  ']));

        $response->assertStatus(200);
        $response->assertViewHas('query', 'Cloud');
    });

    it('includes robots noindex meta tag on search page', function () {
        $response = $this->get(route('search.index', ['q' => 'test']));

        $response->assertStatus(200);
        $response->assertSee('<meta name="robots" content="noindex, follow">', false);
    });
});
