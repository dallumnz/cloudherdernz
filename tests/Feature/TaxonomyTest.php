<?php

use App\Livewire\CategoryManager;
use App\Livewire\TagManager;
use App\Models\Taxonomy;
use App\Models\TaxonomyTerm;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Livewire\Livewire;

describe('Taxonomy Feature Tests', function () {
    beforeEach(function () {
        // Seed roles and permissions
        $this->seed(RolePermissionSeeder::class);

        // Create user with Editor role (has tag and category permissions)
        $this->user = User::factory()->create();
        $this->user->assignRole('Editor');

        // Create taxonomies
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

    describe('Tag CRUD', function () {
        it('can list tags', function () {
            TaxonomyTerm::factory()
                ->count(3)
                ->create(['taxonomy_id' => $this->tagTaxonomy->id]);

            $response = $this->actingAs($this->user)
                ->get(route('tags.index'));

            $response->assertStatus(200);
        });

        it('can create a tag', function () {
            $response = $this->actingAs($this->user)
                ->get(route('tags.create'));

            $response->assertStatus(200);
        });

        it('can store a tag', function () {
            $data = [
                'name' => 'New Tag',
                'slug' => 'new-tag',
                'description' => 'A test tag',
            ];

            $response = $this->actingAs($this->user)
                ->post(route('tags.store'), $data);

            $response->assertRedirect(route('tags.index'));
            $this->assertDatabaseHas('taxonomy_terms', [
                'name' => 'New Tag',
                'slug' => 'new-tag',
            ]);
        });

        it('can show a tag', function () {
            $tag = TaxonomyTerm::factory()->create([
                'taxonomy_id' => $this->tagTaxonomy->id,
            ]);

            $response = $this->actingAs($this->user)
                ->get(route('tags.show', $tag));

            $response->assertStatus(200);
        });

        it('can edit a tag', function () {
            $tag = TaxonomyTerm::factory()->create([
                'taxonomy_id' => $this->tagTaxonomy->id,
            ]);

            $response = $this->actingAs($this->user)
                ->get(route('tags.edit', $tag));

            $response->assertStatus(200);
        });

        it('can update a tag', function () {
            $tag = TaxonomyTerm::factory()->create([
                'taxonomy_id' => $this->tagTaxonomy->id,
            ]);

            $data = [
                'name' => 'Updated Tag',
                'slug' => 'updated-tag',
                'description' => 'Updated description',
            ];

            $response = $this->actingAs($this->user)
                ->put(route('tags.update', $tag), $data);

            $response->assertRedirect(route('tags.index'));
            $this->assertDatabaseHas('taxonomy_terms', [
                'id' => $tag->id,
                'name' => 'Updated Tag',
            ]);
        });

        it('can delete a tag', function () {
            $tag = TaxonomyTerm::factory()->create([
                'taxonomy_id' => $this->tagTaxonomy->id,
            ]);

            $response = $this->actingAs($this->user)
                ->delete(route('tags.destroy', $tag));

            $response->assertRedirect(route('tags.index'));
            $this->assertDatabaseMissing('taxonomy_terms', [
                'id' => $tag->id,
            ]);
        });
    });

    describe('Category CRUD', function () {
        it('can list categories', function () {
            TaxonomyTerm::factory()
                ->count(3)
                ->create([
                    'taxonomy_id' => $this->categoryTaxonomy->id,
                    'parent_id' => null,
                ]);

            $response = $this->actingAs($this->user)
                ->get(route('categories.index'));

            $response->assertStatus(200);
        });

        it('can create a category', function () {
            $response = $this->actingAs($this->user)
                ->get(route('categories.create'));

            $response->assertStatus(200);
        });

        it('can store a category', function () {
            $data = [
                'name' => 'New Category',
                'slug' => 'new-category',
                'description' => 'A test category',
                'parent_id' => null,
            ];

            $response = $this->actingAs($this->user)
                ->post(route('categories.store'), $data);

            $response->assertRedirect(route('categories.index'));
            $this->assertDatabaseHas('taxonomy_terms', [
                'name' => 'New Category',
                'slug' => 'new-category',
            ]);
        });

        it('can store a child category', function () {
            $parent = TaxonomyTerm::factory()->create([
                'taxonomy_id' => $this->categoryTaxonomy->id,
                'parent_id' => null,
            ]);

            $data = [
                'name' => 'Child Category',
                'slug' => 'child-category',
                'description' => 'A child category',
                'parent_id' => $parent->id,
            ];

            $response = $this->actingAs($this->user)
                ->post(route('categories.store'), $data);

            $response->assertRedirect(route('categories.index'));
            $this->assertDatabaseHas('taxonomy_terms', [
                'name' => 'Child Category',
                'parent_id' => $parent->id,
            ]);
        });

        it('can show a category', function () {
            $category = TaxonomyTerm::factory()->create([
                'taxonomy_id' => $this->categoryTaxonomy->id,
            ]);

            $response = $this->actingAs($this->user)
                ->get(route('categories.show', $category));

            $response->assertStatus(200);
        });

        it('can edit a category', function () {
            $category = TaxonomyTerm::factory()->create([
                'taxonomy_id' => $this->categoryTaxonomy->id,
            ]);

            $response = $this->actingAs($this->user)
                ->get(route('categories.edit', $category));

            $response->assertStatus(200);
        });

        it('can update a category', function () {
            $category = TaxonomyTerm::factory()->create([
                'taxonomy_id' => $this->categoryTaxonomy->id,
            ]);

            $data = [
                'name' => 'Updated Category',
                'slug' => 'updated-category',
                'description' => 'Updated description',
                'parent_id' => null,
            ];

            $response = $this->actingAs($this->user)
                ->put(route('categories.update', $category), $data);

            $response->assertRedirect(route('categories.index'));
            $this->assertDatabaseHas('taxonomy_terms', [
                'id' => $category->id,
                'name' => 'Updated Category',
            ]);
        });

        it('can delete a category and reassign children', function () {
            $parent = TaxonomyTerm::factory()->create([
                'taxonomy_id' => $this->categoryTaxonomy->id,
                'parent_id' => null,
            ]);

            $child = TaxonomyTerm::factory()->create([
                'taxonomy_id' => $this->categoryTaxonomy->id,
                'parent_id' => $parent->id,
            ]);

            $response = $this->actingAs($this->user)
                ->delete(route('categories.destroy', $parent));

            $response->assertRedirect(route('categories.index'));
            $this->assertDatabaseMissing('taxonomy_terms', [
                'id' => $parent->id,
            ]);
            $this->assertDatabaseHas('taxonomy_terms', [
                'id' => $child->id,
                'parent_id' => null,
            ]);
        });
    });

    describe('Policy checks', function () {
        it('requires authentication for tag management endpoints', function () {
            $tag = TaxonomyTerm::factory()->create([
                'taxonomy_id' => $this->tagTaxonomy->id,
            ]);

            // Public routes (index, show) are accessible without auth
            $this->get(route('tags.index'))->assertStatus(200);
            $this->get(route('tags.show', $tag))->assertStatus(200);

            // Protected routes require authentication
            $this->get(route('tags.create'))->assertRedirect(route('login'));
            $this->post(route('tags.store'), [])->assertRedirect(route('login'));
            $this->get(route('tags.edit', $tag))->assertRedirect(route('login'));
            $this->put(route('tags.update', $tag), [])->assertRedirect(route('login'));
            $this->delete(route('tags.destroy', $tag))->assertRedirect(route('login'));
        });

        it('requires authentication for category management endpoints', function () {
            $category = TaxonomyTerm::factory()->create([
                'taxonomy_id' => $this->categoryTaxonomy->id,
            ]);

            // Public routes (index, show) are accessible without auth
            $this->get(route('categories.index'))->assertStatus(200);
            $this->get(route('categories.show', $category))->assertStatus(200);

            // Protected routes require authentication
            $this->get(route('categories.create'))->assertRedirect(route('login'));
            $this->post(route('categories.store'), [])->assertRedirect(route('login'));
            $this->get(route('categories.edit', $category))->assertRedirect(route('login'));
            $this->put(route('categories.update', $category), [])->assertRedirect(route('login'));
            $this->delete(route('categories.destroy', $category))->assertRedirect(route('login'));
        });
    });
});

describe('TagManager Livewire Component', function () {
    beforeEach(function () {
        // Seed roles and permissions
        $this->seed(RolePermissionSeeder::class);

        // Create user with Editor role
        $this->user = User::factory()->create();
        $this->user->assignRole('Editor');

        $this->tagTaxonomy = Taxonomy::factory()->create([
            'name' => 'Tags',
            'slug' => 'tags',
            'type' => 'tag',
            'is_hierarchical' => false,
        ]);
    });

    it('renders successfully', function () {
        Livewire::actingAs($this->user)
            ->test(TagManager::class)
            ->assertStatus(200);
    });

    it('can create a tag', function () {
        Livewire::actingAs($this->user)
            ->test(TagManager::class)
            ->set('name', 'Test Tag')
            ->set('slug', 'test-tag')
            ->set('description', 'Test description')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('taxonomy_terms', [
            'name' => 'Test Tag',
            'slug' => 'test-tag',
        ]);
    });

    it('validates required fields', function () {
        Livewire::actingAs($this->user)
            ->test(TagManager::class)
            ->set('name', '')
            ->set('slug', '')
            ->call('save')
            ->assertHasErrors(['name', 'slug']);
    });

    it('can edit a tag', function () {
        $tag = TaxonomyTerm::factory()->create([
            'taxonomy_id' => $this->tagTaxonomy->id,
            'name' => 'Original Name',
        ]);

        Livewire::actingAs($this->user)
            ->test(TagManager::class)
            ->call('edit', $tag->id)
            ->assertSet('name', 'Original Name')
            ->set('name', 'Updated Name')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('taxonomy_terms', [
            'id' => $tag->id,
            'name' => 'Updated Name',
        ]);
    });

    it('can delete a tag', function () {
        $tag = TaxonomyTerm::factory()->create([
            'taxonomy_id' => $this->tagTaxonomy->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(TagManager::class)
            ->call('delete', $tag->id);

        $this->assertDatabaseMissing('taxonomy_terms', [
            'id' => $tag->id,
        ]);
    });

    it('can search tags', function () {
        TaxonomyTerm::factory()->create([
            'taxonomy_id' => $this->tagTaxonomy->id,
            'name' => 'Laravel Tag',
        ]);

        TaxonomyTerm::factory()->create([
            'taxonomy_id' => $this->tagTaxonomy->id,
            'name' => 'PHP Tag',
        ]);

        Livewire::actingAs($this->user)
            ->test(TagManager::class)
            ->set('search', 'Laravel')
            ->assertSee('Laravel Tag')
            ->assertDontSee('PHP Tag');
    });

    it('dispatches events on save', function () {
        Livewire::actingAs($this->user)
            ->test(TagManager::class)
            ->set('name', 'Event Test Tag')
            ->set('slug', 'event-test-tag')
            ->call('save')
            ->assertDispatched('tag-saved');
    });
});

describe('CategoryManager Livewire Component', function () {
    beforeEach(function () {
        // Seed roles and permissions
        $this->seed(RolePermissionSeeder::class);

        // Create user with Editor role
        $this->user = User::factory()->create();
        $this->user->assignRole('Editor');

        $this->categoryTaxonomy = Taxonomy::factory()->create([
            'name' => 'Categories',
            'slug' => 'categories',
            'type' => 'category',
            'is_hierarchical' => true,
        ]);
    });

    it('renders successfully', function () {
        Livewire::actingAs($this->user)
            ->test(CategoryManager::class)
            ->assertStatus(200);
    });

    it('can create a category', function () {
        Livewire::actingAs($this->user)
            ->test(CategoryManager::class)
            ->set('name', 'Test Category')
            ->set('slug', 'test-category')
            ->set('description', 'Test description')
            ->set('parentId', null)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('taxonomy_terms', [
            'name' => 'Test Category',
            'slug' => 'test-category',
            'parent_id' => null,
        ]);
    });

    it('can create a child category', function () {
        $parent = TaxonomyTerm::factory()->create([
            'taxonomy_id' => $this->categoryTaxonomy->id,
            'parent_id' => null,
        ]);

        Livewire::actingAs($this->user)
            ->test(CategoryManager::class)
            ->set('name', 'Child Category')
            ->set('slug', 'child-category')
            ->set('parentId', $parent->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('taxonomy_terms', [
            'name' => 'Child Category',
            'parent_id' => $parent->id,
        ]);
    });

    it('validates required fields', function () {
        Livewire::actingAs($this->user)
            ->test(CategoryManager::class)
            ->set('name', '')
            ->set('slug', '')
            ->call('save')
            ->assertHasErrors(['name', 'slug']);
    });

    it('prevents self-parenting', function () {
        $category = TaxonomyTerm::factory()->create([
            'taxonomy_id' => $this->categoryTaxonomy->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(CategoryManager::class)
            ->call('edit', $category->id)
            ->set('parentId', $category->id)
            ->call('save')
            ->assertHasErrors(['parentId']);
    });

    it('can edit a category', function () {
        $category = TaxonomyTerm::factory()->create([
            'taxonomy_id' => $this->categoryTaxonomy->id,
            'name' => 'Original Name',
        ]);

        Livewire::actingAs($this->user)
            ->test(CategoryManager::class)
            ->call('edit', $category->id)
            ->assertSet('name', 'Original Name')
            ->set('name', 'Updated Name')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('taxonomy_terms', [
            'id' => $category->id,
            'name' => 'Updated Name',
        ]);
    });

    it('can delete a category and reassign children', function () {
        $parent = TaxonomyTerm::factory()->create([
            'taxonomy_id' => $this->categoryTaxonomy->id,
            'parent_id' => null,
        ]);

        $child = TaxonomyTerm::factory()->create([
            'taxonomy_id' => $this->categoryTaxonomy->id,
            'parent_id' => $parent->id,
        ]);

        Livewire::actingAs($this->user)
            ->test(CategoryManager::class)
            ->call('delete', $parent->id);

        $this->assertDatabaseMissing('taxonomy_terms', [
            'id' => $parent->id,
        ]);

        $this->assertDatabaseHas('taxonomy_terms', [
            'id' => $child->id,
            'parent_id' => null,
        ]);
    });

    it('can search categories', function () {
        TaxonomyTerm::factory()->create([
            'taxonomy_id' => $this->categoryTaxonomy->id,
            'name' => 'Development',
            'parent_id' => null,
        ]);

        TaxonomyTerm::factory()->create([
            'taxonomy_id' => $this->categoryTaxonomy->id,
            'name' => 'Marketing',
            'parent_id' => null,
        ]);

        Livewire::actingAs($this->user)
            ->test(CategoryManager::class)
            ->set('search', 'Development')
            ->assertSee('Development')
            ->assertDontSee('Marketing');
    });

    it('dispatches events on save', function () {
        Livewire::actingAs($this->user)
            ->test(CategoryManager::class)
            ->set('name', 'Event Test Category')
            ->set('slug', 'event-test-category')
            ->set('parentId', null)
            ->call('save')
            ->assertDispatched('category-saved');
    });
});
