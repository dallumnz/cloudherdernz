<?php

use App\Models\ImagePost;
use App\Models\Post;
use App\Models\User;

describe('Search Results Page', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
    });

    it('can access the dedicated search results page', function () {
        $response = $this->get(route('search.results', ['q' => 'test']));

        $response->assertStatus(200);
        $response->assertViewIs('search.results');
    });

    it('returns search results on results page when query matches post title', function () {
        $imagePost = ImagePost::factory()->create();
        $post = Post::factory()->published()->create([
            'title' => 'Cloud Computing Results',
            'content' => 'Some content about servers',
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
        ]);

        $response = $this->get(route('search.results', ['q' => 'Cloud']));

        $response->assertStatus(200);
        $response->assertViewIs('search.results');
        $response->assertViewHas('query', 'Cloud');
        $response->assertViewHas('posts');
        $response->assertSee($post->title);
    });

    it('returns search results on results page when query matches post content', function () {
        $imagePost = ImagePost::factory()->create();
        $post = Post::factory()->published()->create([
            'title' => 'Server Management',
            'content' => 'Learn about cloud infrastructure and deployment results',
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
        ]);

        $response = $this->get(route('search.results', ['q' => 'infrastructure']));

        $response->assertStatus(200);
        $response->assertViewIs('search.results');
        $response->assertSee($post->title);
    });

    it('returns empty results on results page when no posts match query', function () {
        $imagePost = ImagePost::factory()->create();
        Post::factory()->published()->create([
            'title' => 'Different Topic',
            'content' => 'Some unrelated content',
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
        ]);

        $response = $this->get(route('search.results', ['q' => 'nonexistent']));

        $response->assertStatus(200);
        $response->assertViewIs('search.results');
        $response->assertSee('No results found');
        $response->assertSee('nonexistent');
    });

    it('only shows published posts in results page search results', function () {
        $imagePost = ImagePost::factory()->create();
        $publishedPost = Post::factory()->published()->create([
            'title' => 'Published Post About Results',
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
        ]);

        $draftImagePost = ImagePost::factory()->create();
        $draftPost = Post::factory()->draft()->create([
            'title' => 'Draft Post About Results',
            'postable_type' => ImagePost::class,
            'postable_id' => $draftImagePost->id,
        ]);

        $response = $this->get(route('search.results', ['q' => 'Results']));

        $response->assertStatus(200);
        $response->assertSee($publishedPost->title);
        $response->assertDontSee($draftPost->title);
    });

    it('validates search query is required on results page', function () {
        $response = $this->get(route('search.results'));

        $response->assertSessionHasErrors(['q']);
    });

    it('validates search query minimum length on results page', function () {
        $response = $this->get(route('search.results', ['q' => 'a']));

        $response->assertSessionHasErrors(['q']);
    });

    it('validates search query maximum length on results page', function () {
        $response = $this->get(route('search.results', ['q' => str_repeat('a', 256)]));

        $response->assertSessionHasErrors(['q']);
    });

    it('paginates search results on results page', function () {
        $imagePosts = ImagePost::factory()->count(15)->create();
        foreach ($imagePosts as $index => $imagePost) {
            Post::factory()->published()->create([
                'title' => "Results Post {$index}",
                'postable_type' => ImagePost::class,
                'postable_id' => $imagePost->id,
            ]);
        }

        $response = $this->get(route('search.results', ['q' => 'Results']));

        $response->assertStatus(200);
        $response->assertViewHas('posts', function ($posts) {
            return $posts->count() <= 12;
        });
    });

    it('displays search form on results page', function () {
        $response = $this->get(route('search.results', ['q' => 'test']));

        $response->assertStatus(200);
        $response->assertSee('Search Results');
        $response->assertSee('value="test"', false);
    });

    it('includes robots noindex meta tag on results page', function () {
        $response = $this->get(route('search.results', ['q' => 'test']));

        $response->assertStatus(200);
        $response->assertSee('<meta name="robots" content="noindex, follow">', false);
    });

    it('rejects whitespace-only search queries on results page', function () {
        $response = $this->get(route('search.results', ['q' => '   ']));

        $response->assertSessionHasErrors(['q']);
    });

    it('trims whitespace from search queries on results page', function () {
        $imagePost = ImagePost::factory()->create();
        Post::factory()->published()->create([
            'title' => 'Cloud Computing Results',
            'content' => 'Content about cloud',
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
        ]);

        $response = $this->get(route('search.results', ['q' => '  Cloud  ']));

        $response->assertStatus(200);
        $response->assertViewHas('query', 'Cloud');
    });

    it('shows no query state when accessing results page without query', function () {
        $response = $this->get(route('search.results'));

        $response->assertSessionHasErrors(['q']);
    });

    it('has working links to browse all posts from no results page', function () {
        $response = $this->get(route('search.results', ['q' => 'nonexistentquery12345']));

        $response->assertStatus(200);
        $response->assertSee('Browse all posts');
        $response->assertSee(route('posts.index'));
    });

    it('has working links to try new search from no results page', function () {
        $response = $this->get(route('search.results', ['q' => 'nonexistentquery12345']));

        $response->assertStatus(200);
        $response->assertSee('Try a new search');
        $response->assertSee(route('search.index'));
    });
});
