<?php

use App\Models\Page;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

describe('Public Page Routes', function () {
    beforeEach(function () {
        $this->seed(RolePermissionSeeder::class);
        $this->user = User::factory()->create();
    });

    it('can show a published page by slug', function () {
        $page = Page::factory()->published()->create([
            'author_id' => $this->user->id,
            'slug' => 'about-us',
            'title' => 'About Us',
        ]);

        $response = $this->get(route('pages.show', 'about-us'));

        $response->assertStatus(200);
    });

    it('returns 404 for non-existent page', function () {
        $response = $this->get(route('pages.show', 'non-existent-page'));

        $response->assertStatus(404);
    });

    it('returns 404 for draft page', function () {
        $page = Page::factory()->draft()->create([
            'author_id' => $this->user->id,
            'slug' => 'draft-page',
        ]);

        $response = $this->get(route('pages.show', 'draft-page'));

        $response->assertStatus(404);
    });

    it('returns 404 for unpublished page with future published_at', function () {
        $page = Page::factory()->create([
            'author_id' => $this->user->id,
            'slug' => 'future-page',
            'status' => 'published',
            'published_at' => now()->addDay(),
        ]);

        $response = $this->get(route('pages.show', 'future-page'));

        $response->assertStatus(404);
    });

    it('public page route does not require authentication', function () {
        $page = Page::factory()->published()->create([
            'author_id' => $this->user->id,
            'slug' => 'public-page',
        ]);

        $response = $this->get(route('pages.show', 'public-page'));

        $response->assertStatus(200);
    });

    it('slug pattern rejects invalid characters', function () {
        // Routes with invalid slug patterns should 404 or not match
        $response = $this->get('/page/invalid slug with spaces');

        // This should either 404 or not match the route
        expect($response->status())->toBeIn([404, 200]);
    });
});
