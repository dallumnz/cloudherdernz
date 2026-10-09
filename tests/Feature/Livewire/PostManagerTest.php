<?php

use App\Enums\PostType;
use App\Livewire\PostManager;
use App\Models\ImagePost;
use App\Models\StandardPost;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Livewire\Livewire;

describe('PostManager Livewire Component', function () {
    beforeEach(function () {
        $this->seed(RolePermissionSeeder::class);
        $this->user = User::factory()->create();
        $this->user->assignRole('Editor');
    });

    it('creates a StandardPost when no post type is selected', function () {
        Livewire::actingAs($this->user)
            ->test(PostManager::class)
            ->set('title', 'Untyped Post')
            ->set('slug', 'untyped-post')
            ->set('status', 'draft')
            ->call('save')
            ->assertDispatched('post-saved');

        $post = \App\Models\Post::where('slug', 'untyped-post')->first();

        expect($post)->not->toBeNull();
        expect($post->postable_type)->toBe(StandardPost::class);
        expect($post->postable)->toBeInstanceOf(StandardPost::class);
        expect($post->isType(PostType::STANDARD))->toBeTrue();
    });

    it('creates an ImagePost when an image type is selected', function () {
        Livewire::actingAs($this->user)
            ->test(PostManager::class)
            ->set('title', 'Image Post')
            ->set('slug', 'image-post-test')
            ->set('postTypeValue', PostType::IMAGE->model())
            ->set('status', 'draft')
            ->call('save')
            ->assertDispatched('post-saved');

        $post = \App\Models\Post::where('slug', 'image-post-test')->first();

        expect($post)->not->toBeNull();
        expect($post->postable_type)->toBe(ImagePost::class);
        expect($post->postable)->toBeInstanceOf(ImagePost::class);
    });
});
