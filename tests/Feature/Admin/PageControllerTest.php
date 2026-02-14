<?php

use App\Models\Page;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

describe('Admin Page Controller', function () {
    beforeEach(function () {
        $this->seed(RolePermissionSeeder::class);
        $this->user = User::factory()->create();
        $this->user->assignRole('Editor');
    });

    it('can list pages', function () {
        Page::factory()->count(3)->create(['author_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->get(route('admin.pages.index'));

        $response->assertStatus(200);
    });

    it('can filter pages by status', function () {
        $publishedPage = Page::factory()->published()->create(['author_id' => $this->user->id]);
        $draftPage = Page::factory()->draft()->create(['author_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->get(route('admin.pages.index', ['status' => 'published']));

        $response->assertStatus(200);
    });

    it('can create a page', function () {
        $response = $this->actingAs($this->user)
            ->get(route('admin.pages.create'));

        $response->assertStatus(200);
    });

    it('can store a page', function () {
        $response = $this->actingAs($this->user)
            ->post(route('admin.pages.store'), [
                'title' => 'Test Page',
                'slug' => 'test-page',
                'content' => 'Test content',
                'status' => 'published',
            ]);

        $response->assertRedirect(route('admin.pages.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pages', [
            'title' => 'Test Page',
            'slug' => 'test-page',
            'status' => 'published',
        ]);
    });

    it('validates required fields when storing', function () {
        $response = $this->actingAs($this->user)
            ->post(route('admin.pages.store'), []);

        $response->assertSessionHasErrors(['title', 'slug', 'status']);
    });

    it('validates unique slug when storing', function () {
        Page::factory()->create([
            'author_id' => $this->user->id,
            'slug' => 'existing-slug',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('admin.pages.store'), [
                'title' => 'Test Page',
                'slug' => 'existing-slug',
                'status' => 'published',
            ]);

        $response->assertSessionHasErrors(['slug']);
    });

    it('can show a page', function () {
        $page = Page::factory()->create(['author_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->get(route('admin.pages.show', $page));

        $response->assertStatus(200);
    });

    it('can edit a page', function () {
        $page = Page::factory()->create(['author_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->get(route('admin.pages.edit', $page));

        $response->assertStatus(200);
    });

    it('can update a page', function () {
        $page = Page::factory()->create([
            'author_id' => $this->user->id,
            'title' => 'Original Title',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('admin.pages.update', $page), [
                'title' => 'Updated Title',
                'slug' => 'updated-slug',
                'content' => 'Updated content',
                'status' => 'published',
            ]);

        $response->assertRedirect(route('admin.pages.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'title' => 'Updated Title',
            'slug' => 'updated-slug',
            'status' => 'published',
        ]);
    });

    it('can update page with same slug', function () {
        $page = Page::factory()->create([
            'author_id' => $this->user->id,
            'slug' => 'same-slug',
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('admin.pages.update', $page), [
                'title' => 'Updated Title',
                'slug' => 'same-slug',
                'status' => 'published',
            ]);

        $response->assertRedirect(route('admin.pages.index'));
        $response->assertSessionHas('success');
    });

    it('can delete a page', function () {
        $page = Page::factory()->create(['author_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->delete(route('admin.pages.destroy', $page));

        $response->assertRedirect(route('admin.pages.index'));
        $response->assertSessionHas('success');

        // Page should be soft deleted (not visible in normal queries)
        $this->assertNull(Page::find($page->id));
        // But still exists in database with deleted_at
        $this->assertNotNull(Page::withTrashed()->find($page->id));
    });

    it('prevents unauthorized users from creating pages', function () {
        $unauthorizedUser = User::factory()->create();

        $response = $this->actingAs($unauthorizedUser)
            ->post(route('admin.pages.store'), [
                'title' => 'Unauthorized Page',
                'slug' => 'unauthorized-page',
                'status' => 'draft',
            ]);

        $response->assertStatus(403);
    });

    it('prevents unauthorized users from editing pages', function () {
        $unauthorizedUser = User::factory()->create();
        $page = Page::factory()->create(['author_id' => $this->user->id]);

        $response = $this->actingAs($unauthorizedUser)
            ->get(route('admin.pages.edit', $page));

        $response->assertStatus(403);
    });

    it('prevents unauthorized users from deleting pages', function () {
        $unauthorizedUser = User::factory()->create();
        $page = Page::factory()->create(['author_id' => $this->user->id]);

        $response = $this->actingAs($unauthorizedUser)
            ->delete(route('admin.pages.destroy', $page));

        $response->assertStatus(403);
    });

    it('requires authentication to access pages', function () {
        $response = $this->get(route('admin.pages.index'));

        $response->assertRedirect(route('login'));
    });
});
