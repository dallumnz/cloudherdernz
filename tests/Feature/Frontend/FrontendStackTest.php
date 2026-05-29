<?php

use App\Enums\PostType;
use App\Models\AudioPost;
use App\Models\ImagePost;
use App\Models\Post;
use App\Models\Taxonomy;
use App\Models\TaxonomyTerm;
use App\Models\User;
use App\Models\VideoPost;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses()->group('frontend');
uses(RefreshDatabase::class);

beforeEach(function () {
    // Run seeders
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);

    // Create taxonomies
    $tagTaxonomy = Taxonomy::factory()->create([
        'type' => 'tag',
        'name' => 'Tags',
        'slug' => 'tags',
    ]);

    $categoryTaxonomy = Taxonomy::factory()->create([
        'type' => 'category',
        'name' => 'Categories',
        'slug' => 'categories',
        'is_hierarchical' => true,
    ]);

    // Create users
    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    $author = User::factory()->create();
    $author->assignRole('Author');

    // Create ImagePost and Post
    $imagePostable = ImagePost::factory()->create([
        'caption' => 'Test image caption',
    ]);

    $imagePost = Post::factory()->create([
        'author_id' => $author->id,
        'postable_type' => ImagePost::class,
        'postable_id' => $imagePostable->id,
        'status' => 'published',
        'published_at' => now()->subDay(),
    ]);

    // Create VideoPost and Post
    $videoPostable = VideoPost::factory()->create([
        'video_url' => 'https://youtube.com/watch?v=test',
        'provider' => 'youtube',
    ]);

    $videoPost = Post::factory()->create([
        'author_id' => $author->id,
        'postable_type' => VideoPost::class,
        'postable_id' => $videoPostable->id,
        'status' => 'published',
        'published_at' => now()->subHours(2),
    ]);

    // Create taxonomy terms
    $tag = TaxonomyTerm::factory()->create([
        'taxonomy_id' => $tagTaxonomy->id,
        'name' => 'Photography',
    ]);

    $category = TaxonomyTerm::factory()->create([
        'taxonomy_id' => $categoryTaxonomy->id,
        'name' => 'Tutorials',
    ]);

    // Attach taxonomy terms to posts
    $imagePost->taxonomyTerms()->attach([$tag->id, $category->id]);

    // Store in test instance
    $this->tagTaxonomy = $tagTaxonomy;
    $this->categoryTaxonomy = $categoryTaxonomy;
    $this->admin = $admin;
    $this->author = $author;
    $this->imagePost = $imagePost;
    $this->videoPost = $videoPost;
    $this->tag = $tag;
    $this->category = $category;
});

describe('Public Homepage', function () {
    it('displays the public homepage with featured posts', function () {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('CloudHerder.nz');
        $response->assertSee($this->imagePost->title);
        $response->assertSee($this->videoPost->title);
    });

    it('displays post type links in navigation', function () {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Image Post');
        $response->assertSee('Video Post');
    });
});

describe('Post Type Filter Pages', function () {
    it('displays posts filtered by image type', function () {
        $response = $this->get(route('posts.by-type', 'image'));

        $response->assertStatus(200);
        $response->assertSee('Image Posts');
        $response->assertSee($this->imagePost->title);
        $response->assertDontSee($this->videoPost->title);
    });

    it('displays posts filtered by video type', function () {
        $response = $this->get(route('posts.by-type', 'video'));

        $response->assertStatus(200);
        $response->assertSee('Video Posts');
        $response->assertSee($this->videoPost->title);
        $response->assertDontSee($this->imagePost->title);
    });

    it('displays all posts when no type is specified', function () {
        $response = $this->get(route('posts.index'));

        $response->assertStatus(200);
        $response->assertSee($this->imagePost->title);
        $response->assertSee($this->videoPost->title);
    });
});

describe('Tag Pages', function () {
    it('displays tag index page', function () {
        $response = $this->get(route('tags.index'));

        $response->assertStatus(200);
        $response->assertSee('Tags');
        $response->assertSee($this->tag->name);
    });

    it('displays tag detail page with posts', function () {
        $response = $this->get(route('tags.show', $this->tag));

        $response->assertStatus(200);
        $response->assertSee($this->tag->name);
        $response->assertSee($this->imagePost->title);
    });
});

describe('Category Pages', function () {
    it('displays category index page', function () {
        $response = $this->get(route('categories.index'));

        $response->assertStatus(200);
        $response->assertSee('Categories');
        $response->assertSee($this->category->name);
    });

    it('displays category detail page with posts', function () {
        $response = $this->get(route('categories.show', $this->category));

        $response->assertStatus(200);
        $response->assertSee($this->category->name);
        $response->assertSee($this->imagePost->title);
    });
});

describe('Admin Dashboard', function () {
    it('redirects /admin to dashboard for authorized users', function () {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertRedirect(route('dashboard'));
    });

    it('shows admin stats on unified dashboard for authorized users', function () {
        $response = $this->actingAs($this->admin)
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Total Posts');
        $response->assertSee('Your Role');
        $response->assertSee('Recent Users');
    });

    it('redirects guests from admin dashboard', function () {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    });
});

describe('Post Management', function () {
    it('displays post manager for authorized users', function () {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.posts'));

        $response->assertStatus(200);
        $response->assertSee('Post Management');
        $response->assertSee($this->imagePost->title);
    });

    it('allows creating posts through livewire', function () {
        $this->actingAs($this->admin);

        \Livewire::test(\App\Livewire\PostManager::class)
            ->set('title', 'New Test Post')
            ->set('slug', 'new-test-post')
            ->set('content', 'Test content')
            ->set('status', 'published')
            ->call('save')
            ->assertSet('message', 'Post \'New Test Post\' created successfully.');

        $this->assertDatabaseHas('posts', [
            'title' => 'New Test Post',
            'slug' => 'new-test-post',
        ]);
    });
});

describe('Tag Management', function () {
    it('displays tag manager for authorized users', function () {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.tags'));

        $response->assertStatus(200);
        $response->assertSee('Tag Management');
    });
});

describe('Category Management', function () {
    it('displays category manager for authorized users', function () {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories'));

        $response->assertStatus(200);
        $response->assertSee('Category Management');
    });
});

describe('User Management', function () {
    it('displays user manager for authorized users', function () {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users'));

        $response->assertStatus(200);
        $response->assertSee('User Management');
    });
});

describe('Role Management', function () {
    it('displays role manager for authorized users', function () {
        $response = $this->actingAs($this->admin)
            ->get(route('roles.manage'));

        $response->assertStatus(200);
        $response->assertSee('Role Management');
    });
});

describe('Media Library', function () {
    it('displays media library for authorized users', function () {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.media.index'));

        $response->assertStatus(200);
        $response->assertSee('Media Library');
    });
});
