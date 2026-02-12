<?php

use App\Enums\PostType;
use App\Models\AudioPost;
use App\Models\ImagePost;
use App\Models\Post;
use App\Models\Taxonomy;
use App\Models\TaxonomyTerm;
use App\Models\User;
use App\Models\VideoPost;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function (): void {
    // Seed roles and permissions
    $this->seed(RolePermissionSeeder::class);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('Admin');

    $this->author = User::factory()->create();
    $this->author->assignRole('Author');

    $this->viewer = User::factory()->create();
    $this->viewer->assignRole('Viewer');

    // Create taxonomy and term
    $this->taxonomy = Taxonomy::factory()->create([
        'name' => 'Tags',
        'slug' => 'tags',
        'type' => 'tag',
    ]);

    $this->term = TaxonomyTerm::factory()->create([
        'taxonomy_id' => $this->taxonomy->id,
        'name' => 'Laravel',
        'slug' => 'laravel',
    ]);
});

/**
 * Helper to create a post with a specific type.
 */
function createPostWithType(PostType $type, array $attributes = []): Post
{
    $author = $attributes['author_id'] ?? User::factory()->create();
    $author->assignRole('Author');

    // Create the postable first to get its ID
    $postable = match ($type) {
        PostType::IMAGE => ImagePost::factory()->create(),
        PostType::VIDEO => VideoPost::factory()->create(),
        PostType::AUDIO => AudioPost::factory()->create(),
    };

    // Now create the post with both polymorphic columns
    $post = Post::factory()->create(array_merge([
        'author_id' => $author->id,
        'postable_type' => get_class($postable),
        'postable_id' => $postable->id,
        'status' => 'published',
        'published_at' => now()->subDay(),
    ], $attributes));

    return $post;
}

describe('Post API Index', function (): void {
    it('returns published posts for public access', function (): void {
        $posts = [];
        foreach (range(1, 3) as $i) {
            $posts[] = createPostWithType(PostType::IMAGE, [
                'status' => 'published',
                'published_at' => now()->subDay(),
            ]);
        }

        // Draft posts
        foreach (range(1, 2) as $i) {
            createPostWithType(PostType::VIDEO, [
                'status' => 'draft',
            ]);
        }

        $response = $this->getJson(route('api.posts.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'slug',
                        'excerpt',
                        'content',
                        'featured_image',
                        'author',
                        'status',
                        'published_at',
                        'type',
                        'taxonomy_terms',
                        'metadata',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'links',
                'meta',
            ])
            ->assertJsonCount(3, 'data');
    });

    it('filters posts by taxonomy term', function (): void {
        $post = createPostWithType(PostType::IMAGE, [
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);
        $post->taxonomyTerms()->attach($this->term);

        createPostWithType(PostType::VIDEO, [
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->getJson(route('api.posts.index', ['term' => 'laravel']));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.taxonomy_terms.0.slug', 'laravel');
    });

    it('searches posts by query string', function (): void {
        createPostWithType(PostType::IMAGE, [
            'status' => 'published',
            'published_at' => now()->subDay(),
            'title' => 'Laravel Tips and Tricks',
        ]);

        createPostWithType(PostType::VIDEO, [
            'status' => 'published',
            'published_at' => now()->subDay(),
            'title' => 'Vue.js Guide',
        ]);

        $response = $this->getJson(route('api.posts.index', ['search' => 'Laravel']));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Laravel Tips and Tricks');
    });

    it('sorts posts by different fields', function (): void {
        $first = createPostWithType(PostType::AUDIO, [
            'status' => 'published',
            'published_at' => now()->subDays(2),
            'title' => 'First Post',
        ]);

        createPostWithType(PostType::IMAGE, [
            'status' => 'published',
            'published_at' => now()->subDay(),
            'title' => 'Second Post',
        ]);

        $response = $this->getJson(route('api.posts.index', [
            'sort_by' => 'published_at',
            'sort_order' => 'asc',
        ]));

        $response->assertStatus(200)
            ->assertJsonPath('data.0.title', 'First Post');
    });

    it('paginates results correctly', function (): void {
        foreach (range(1, 25) as $i) {
            createPostWithType(PostType::IMAGE, [
                'status' => 'published',
                'published_at' => now()->subDay(),
            ]);
        }

        $response = $this->getJson(route('api.posts.index', ['per_page' => 10]));

        $response->assertStatus(200)
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonPath('meta.total', 25)
            ->assertJsonCount(10, 'data');
    });
});

describe('Post API Show', function (): void {
    it('returns a single published post', function (): void {
        $post = createPostWithType(PostType::IMAGE, [
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);
        $post->taxonomyTerms()->attach($this->term);

        $response = $this->getJson(route('api.posts.show', $post));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'slug',
                    'excerpt',
                    'content',
                    'featured_image',
                    'author',
                    'status',
                    'published_at',
                    'type',
                    'taxonomy_terms',
                    'metadata',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJsonPath('data.id', $post->id)
            ->assertJsonPath('data.slug', $post->slug);
    });

    it('returns 403 for unpublished posts without permission', function (): void {
        $post = createPostWithType(PostType::VIDEO, [
            'status' => 'draft',
        ]);

        $response = $this->getJson(route('api.posts.show', $post));

        $response->assertStatus(403)
            ->assertJson(['message' => 'This post is not available.']);
    });

    it('returns unpublished post for authorized users', function (): void {
        $post = createPostWithType(PostType::AUDIO, [
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson(route('api.posts.show', $post));

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $post->id);
    });
});

describe('Post API By Type', function (): void {
    it('returns posts filtered by post type', function (): void {
        // Create image posts
        foreach (range(1, 3) as $i) {
            createPostWithType(PostType::IMAGE, [
                'status' => 'published',
                'published_at' => now()->subDay(),
            ]);
        }

        // Create video posts
        foreach (range(1, 2) as $i) {
            createPostWithType(PostType::VIDEO, [
                'status' => 'published',
                'published_at' => now()->subDay(),
            ]);
        }

        $response = $this->getJson(route('api.posts.by-type', 'image'));

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.0.type.slug', 'image');
    });

    it('returns 404 for non-existent post type', function (): void {
        $response = $this->getJson(route('api.posts.by-type', 'non-existent'));

        $response->assertStatus(404)
            ->assertJson(['message' => 'Post type not found.']);
    });

    it('searches within a specific post type', function (): void {
        createPostWithType(PostType::IMAGE, [
            'status' => 'published',
            'published_at' => now()->subDay(),
            'title' => 'Laravel Article',
        ]);

        createPostWithType(PostType::VIDEO, [
            'status' => 'published',
            'published_at' => now()->subDay(),
            'title' => 'Laravel Video',
        ]);

        $response = $this->getJson(route('api.posts.by-type', [
            'type' => 'image',
            'search' => 'Laravel',
        ]));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.type.slug', 'image');
    });
});

describe('Post API Search', function (): void {
    it('returns search results for valid query', function (): void {
        createPostWithType(PostType::IMAGE, [
            'status' => 'published',
            'published_at' => now()->subDay(),
            'title' => 'Advanced Laravel Techniques',
            'excerpt' => 'Learn advanced Laravel patterns',
        ]);

        createPostWithType(PostType::VIDEO, [
            'status' => 'published',
            'published_at' => now()->subDay(),
            'title' => 'Vue.js Basics',
        ]);

        $response = $this->getJson(route('api.posts.search', ['q' => 'Laravel']));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Advanced Laravel Techniques');
    });

    it('returns 422 for short search query', function (): void {
        $response = $this->getJson(route('api.posts.search', ['q' => 'a']));

        $response->assertStatus(422)
            ->assertJson(['message' => 'Search query must be at least 2 characters.']);
    });

    it('returns 422 for missing search query', function (): void {
        $response = $this->getJson(route('api.posts.search'));

        $response->assertStatus(422)
            ->assertJson(['message' => 'Search query must be at least 2 characters.']);
    });

    it('searches across title, excerpt, content, and slug', function (): void {
        createPostWithType(PostType::IMAGE, [
            'status' => 'published',
            'published_at' => now()->subDay(),
            'title' => 'Different Title',
            'excerpt' => 'Laravel excerpt here',
        ]);

        createPostWithType(PostType::VIDEO, [
            'status' => 'published',
            'published_at' => now()->subDay(),
            'title' => 'Another Post',
            'content' => 'Laravel content here',
        ]);

        $response = $this->getJson(route('api.posts.search', ['q' => 'Laravel']));

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    });
});
