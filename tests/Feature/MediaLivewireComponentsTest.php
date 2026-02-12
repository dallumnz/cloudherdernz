<?php

use App\Models\ImagePost;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

describe('FeaturedImageUploader Livewire Component', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();

        // Create ImagePost with Post
        $imagePostable = ImagePost::factory()->create();
        $this->post = Post::factory()->create([
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePostable->id,
            'author_id' => $this->user->id,
        ]);
    });

    it('can render the component', function () {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\FeaturedImageUploader::class, ['postId' => $this->post->id])
            ->assertStatus(200);
    });

    it('can upload a featured image', function () {
        $file = UploadedFile::fake()->image('featured.jpg', 1200, 630);

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\FeaturedImageUploader::class, ['postId' => $this->post->id])
            ->set('image', $file)
            ->call('save')
            ->assertHasNoErrors();

        // User implements: Assert image was uploaded
        // expect($this->post->getFirstMedia('featured'))->not->toBeNull();
    });

    it('validates image is required', function () {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\FeaturedImageUploader::class, ['postId' => $this->post->id])
            ->call('save')
            ->assertHasErrors(['image' => 'required']);
    });

    it('can remove a featured image', function () {
        // First add an image
        $file = UploadedFile::fake()->image('featured.jpg');
        $media = $this->post->addMedia($file)->toMediaCollection('featured');

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\FeaturedImageUploader::class, ['postId' => $this->post->id])
            ->call('removeImage', $media->id)
            ->assertHasNoErrors();

        // User implements: Assert image was removed
        // expect($this->post->getFirstMedia('featured'))->toBeNull();
    });

    it('requires edit posts permission to upload', function () {
        $userWithoutPermission = User::factory()->create();
        $file = UploadedFile::fake()->image('featured.jpg');

        Livewire::actingAs($userWithoutPermission)
            ->test(\App\Livewire\FeaturedImageUploader::class, ['postId' => $this->post->id])
            ->set('image', $file)
            ->call('save')
            ->assertSee('You do not have permission');
    });
});

describe('GalleryManager Livewire Component', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();

        // Create ImagePost with Post
        $imagePostable = ImagePost::factory()->create();
        $this->post = Post::factory()->create([
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePostable->id,
            'author_id' => $this->user->id,
        ]);
    });

    it('can render the component', function () {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\GalleryManager::class, ['postId' => $this->post->id])
            ->assertStatus(200);
    });

    it('can upload multiple gallery images', function () {
        $files = [
            UploadedFile::fake()->image('gallery1.jpg'),
            UploadedFile::fake()->image('gallery2.jpg'),
        ];

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\GalleryManager::class, ['postId' => $this->post->id])
            ->set('images', $files)
            ->call('save')
            ->assertHasNoErrors();

        // User implements: Assert images were uploaded
        // expect($this->post->getMedia('gallery'))->toHaveCount(2);
    });

    it('can remove a gallery image', function () {
        // First add images
        $file = UploadedFile::fake()->image('gallery.jpg');
        $media = $this->post->addMedia($file)->toMediaCollection('gallery');

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\GalleryManager::class, ['postId' => $this->post->id])
            ->call('removeImage', $media->id)
            ->assertHasNoErrors();

        // User implements: Assert image was removed
        // expect($this->post->getMedia('gallery'))->toHaveCount(0);
    });

    it('can reorder gallery images', function () {
        // Add multiple images
        $file1 = UploadedFile::fake()->image('gallery1.jpg');
        $file2 = UploadedFile::fake()->image('gallery2.jpg');
        $media1 = $this->post->addMedia($file1)->toMediaCollection('gallery');
        $media2 = $this->post->addMedia($file2)->toMediaCollection('gallery');

        $orderedIds = [$media2->id, $media1->id];

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\GalleryManager::class, ['postId' => $this->post->id])
            ->call('reorderImages', $orderedIds)
            ->assertHasNoErrors();

        // User implements: Assert order was updated
        // $this->post->refresh();
        // expect($this->post->getMedia('gallery')->first()->id)->toBe($media2->id);
    });

    it('validates maximum 10 images at once', function () {
        $files = array_fill(0, 11, UploadedFile::fake()->image('gallery.jpg'));

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\GalleryManager::class, ['postId' => $this->post->id])
            ->set('images', $files)
            ->assertHasErrors(['images']);
    });
});

describe('MediaUploader Livewire Component', function () {
    beforeEach(function () {
        // Seed roles and permissions
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $this->user = User::factory()->create();
        $this->user->assignRole('Admin'); // Admin has all permissions including media
    });

    it('can render the component', function () {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\MediaUploader::class)
            ->assertStatus(200);
    });

    it('can upload multiple files', function () {
        $files = [
            UploadedFile::fake()->image('image1.jpg'),
            UploadedFile::fake()->image('image2.jpg'),
        ];

        Livewire::actingAs($this->user)
            ->test(\App\Livewire\MediaUploader::class)
            ->set('files', $files)
            ->call('save')
            ->assertHasNoErrors();
    });

    it('can search media', function () {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\MediaUploader::class)
            ->set('search', 'test-image')
            ->assertSet('search', 'test-image');
    });

    it('can sort media by different fields', function () {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\MediaUploader::class)
            ->set('sortField', 'name')
            ->assertSet('sortField', 'name');
    });

    it('requires upload media permission', function () {
        $userWithoutPermission = User::factory()->create();
        $file = UploadedFile::fake()->image('image.jpg');

        Livewire::actingAs($userWithoutPermission)
            ->test(\App\Livewire\MediaUploader::class)
            ->set('files', [$file])
            ->call('save')
            ->assertSee('You do not have permission');
    });

    it('requires delete media permission to delete', function () {
        $userWithoutPermission = User::factory()->create();
        $userWithoutPermission->givePermissionTo('view media');

        Livewire::actingAs($userWithoutPermission)
            ->test(\App\Livewire\MediaUploader::class)
            ->call('deleteMedia', 1)
            ->assertSee('You do not have permission');
    });
});
