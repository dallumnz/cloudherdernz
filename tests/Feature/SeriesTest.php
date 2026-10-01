<?php

use App\Models\Post;
use App\Models\Series;
use App\Models\Taxonomy;
use App\Models\TaxonomyTerm;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->user->givePermissionTo('view series', 'create series', 'edit series', 'delete series');
});

it('can create a series', function () {
    $response = $this->get(route('admin.series'));
    $response->assertStatus(200);

    Livewire::test(\App\Livewire\SeriesManager::class)
        ->set('title', 'Infrastructure for Independence')
        ->set('slug', 'infrastructure-for-independence')
        ->set('tagline', 'Build your own cloud')
        ->set('description', 'A series about self-hosting.')
        ->set('status', 'published')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('series', [
        'title' => 'Infrastructure for Independence',
        'slug' => 'infrastructure-for-independence',
        'status' => 'published',
    ]);
});

it('can create a series with a new tag', function () {
    Livewire::test(\App\Livewire\SeriesManager::class)
        ->set('title', 'Infrastructure for Independence')
        ->set('slug', 'infrastructure-for-independence')
        ->set('newTagName', 'Infrastructure for Independence')
        ->set('status', 'published')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('taxonomies', [
        'slug' => 'series',
        'type' => 'series',
    ]);

    $this->assertDatabaseHas('taxonomy_terms', [
        'name' => 'Infrastructure for Independence',
        'slug' => 'infrastructure-for-independence',
    ]);

    $this->assertDatabaseHas('series', [
        'slug' => 'infrastructure-for-independence',
    ]);
});

it('displays a published series with linked posts', function () {
    $taxonomy = Taxonomy::factory()->create([
        'slug' => 'series',
        'type' => 'series',
        'name' => 'Series',
    ]);

    $term = TaxonomyTerm::factory()->create([
        'taxonomy_id' => $taxonomy->id,
        'name' => 'Infrastructure for Independence',
        'slug' => 'infrastructure-for-independence',
    ]);

    $series = Series::factory()->create([
        'title' => 'Infrastructure for Independence',
        'slug' => 'infrastructure-for-independence',
        'status' => 'published',
        'taxonomy_term_id' => $term->id,
        'published_at' => now(),
    ]);

    $post = Post::factory()->create([
        'status' => 'published',
        'published_at' => now(),
    ]);
    $post->taxonomyTerms()->attach($term);

    $response = $this->get(route('series.show', $series->slug));
    $response->assertStatus(200);
    $response->assertSee('Infrastructure for Independence');
    $response->assertSee($post->title);
});

it('returns 404 for draft series', function () {
    $series = Series::factory()->create([
        'slug' => 'draft-series',
        'status' => 'draft',
    ]);

    $response = $this->get(route('series.show', $series->slug));
    $response->assertStatus(404);
});
