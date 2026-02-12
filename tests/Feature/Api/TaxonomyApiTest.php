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

    $this->user = User::factory()->create();

    $this->tagTaxonomy = Taxonomy::factory()->create([
        'name' => 'Tags',
        'slug' => 'tags',
        'type' => 'tag',
        'is_hierarchical' => false,
    ]);

    $this->categoryTaxonomy = Taxonomy::factory()->create([
        'name' => 'Categories',
        'slug' => 'categories',
        'type' => 'category',
        'is_hierarchical' => true,
    ]);
});

describe('Taxonomy API Index', function (): void {
    it('returns all taxonomies', function (): void {
        $response = $this->getJson(route('api.taxonomies.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'description',
                        'type',
                        'is_hierarchical',
                        'terms_count',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertJsonCount(2, 'data');
    });

    it('filters taxonomies by type', function (): void {
        $response = $this->getJson(route('api.taxonomies.index', ['type' => 'tag']));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.type', 'tag');
    });

    it('searches taxonomies by name', function (): void {
        $response = $this->getJson(route('api.taxonomies.index', ['search' => 'Tag']));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Tags');
    });
});

describe('Taxonomy API Show', function (): void {
    it('returns a single taxonomy with terms', function (): void {
        $term = TaxonomyTerm::factory()->create([
            'taxonomy_id' => $this->tagTaxonomy->id,
            'name' => 'Laravel',
            'slug' => 'laravel',
        ]);

        $response = $this->getJson(route('api.taxonomies.show', $this->tagTaxonomy));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'type',
                    'is_hierarchical',
                    'terms' => [
                        '*' => [
                            'id',
                            'name',
                            'slug',
                        ],
                    ],
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJsonPath('data.terms.0.slug', 'laravel');
    });
});

describe('Taxonomy Terms API Index', function (): void {
    it('returns all taxonomy terms', function (): void {
        TaxonomyTerm::factory()->count(3)->create([
            'taxonomy_id' => $this->tagTaxonomy->id,
        ]);

        $response = $this->getJson(route('api.terms.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta',
            ])
            ->assertJsonCount(3, 'data');
    });

    it('filters terms by taxonomy', function (): void {
        TaxonomyTerm::factory()->count(2)->create([
            'taxonomy_id' => $this->tagTaxonomy->id,
        ]);

        TaxonomyTerm::factory()->count(3)->create([
            'taxonomy_id' => $this->categoryTaxonomy->id,
        ]);

        $response = $this->getJson(route('api.terms.index', ['taxonomy' => 'tags']));

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    });

    it('filters terms by type', function (): void {
        TaxonomyTerm::factory()->count(2)->create([
            'taxonomy_id' => $this->tagTaxonomy->id,
        ]);

        TaxonomyTerm::factory()->count(3)->create([
            'taxonomy_id' => $this->categoryTaxonomy->id,
        ]);

        $response = $this->getJson(route('api.terms.index', ['type' => 'category']));

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    });

    it('filters terms by parent for hierarchical taxonomies', function (): void {
        $parent = TaxonomyTerm::factory()->create([
            'taxonomy_id' => $this->categoryTaxonomy->id,
            'name' => 'Parent Category',
        ]);

        TaxonomyTerm::factory()->count(2)->create([
            'taxonomy_id' => $this->categoryTaxonomy->id,
            'parent_id' => $parent->id,
        ]);

        $response = $this->getJson(route('api.terms.index', ['parent_id' => $parent->id]));

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    });

    it('searches terms by name', function (): void {
        TaxonomyTerm::factory()->create([
            'taxonomy_id' => $this->tagTaxonomy->id,
            'name' => 'Laravel',
        ]);

        TaxonomyTerm::factory()->create([
            'taxonomy_id' => $this->tagTaxonomy->id,
            'name' => 'Vue.js',
        ]);

        $response = $this->getJson(route('api.terms.index', ['search' => 'Laravel']));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Laravel');
    });
});

describe('Taxonomy Terms API Show', function (): void {
    it('returns a single taxonomy term', function (): void {
        $term = TaxonomyTerm::factory()->create([
            'taxonomy_id' => $this->tagTaxonomy->id,
            'name' => 'Laravel',
            'slug' => 'laravel',
        ]);

        $response = $this->getJson(route('api.terms.show', $term));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'taxonomy',
                    'parent',
                    'children_count',
                    'metadata',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJsonPath('data.slug', 'laravel');
    });
});

describe('Taxonomy Terms API Posts', function (): void {
    it('returns posts for a taxonomy term', function (): void {
        $author = User::factory()->create();

        $term = TaxonomyTerm::factory()->create([
            'taxonomy_id' => $this->tagTaxonomy->id,
        ]);

        // Create postable first
        $imagePost = ImagePost::factory()->create();

        $post = Post::factory()->create([
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
            'author_id' => $author->id,
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);
        $post->taxonomyTerms()->attach($term);

        // Create another post without the term
        $videoPost = VideoPost::factory()->create();
        Post::factory()->create([
            'postable_type' => VideoPost::class,
            'postable_id' => $videoPost->id,
            'author_id' => $author->id,
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->getJson(route('api.terms.posts', $term));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $post->id);
    });
});
