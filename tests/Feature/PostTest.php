<?php

use App\Enums\PostType;
use App\Models\AudioPost;
use App\Models\ImagePost;
use App\Models\NewsletterPost;
use App\Models\Post;
use App\Models\User;
use App\Models\VideoPost;
use Database\Seeders\RolePermissionSeeder;

describe('Post Feature', function () {
    beforeEach(function () {
        $this->seed(RolePermissionSeeder::class);
        $this->user = User::factory()->create();
        $this->user->assignRole('Editor');
    });

    it('can list posts', function () {
        // Create posts with polymorphic types
        $posts = [];
        foreach (range(1, 3) as $i) {
            $type = fake()->randomElement(PostType::cases());
            $postable = match ($type) {
                PostType::IMAGE => ImagePost::factory()->create(),
                PostType::VIDEO => VideoPost::factory()->create(),
                PostType::AUDIO => AudioPost::factory()->create(),
                PostType::NEWSLETTER => NewsletterPost::factory()->create(),
            };
            $posts[] = Post::factory()->create([
                'postable_type' => get_class($postable),
                'postable_id' => $postable->id,
            ]);
        }

        $response = $this->actingAs($this->user)
            ->get(route('posts.index'));

        $response->assertStatus(200);
        $response->assertViewIs('posts.index');
        $response->assertViewHas('posts');
    });

    it('can create a post', function () {
        $response = $this->actingAs($this->user)
            ->get(route('posts.create'));

        $response->assertStatus(200);
        $response->assertViewIs('posts.create');
        $response->assertViewHas('postTypes');
        $response->assertViewHas('taxonomyTerms');
    });

    it('can store an image post', function () {
        $response = $this->actingAs($this->user)
            ->post(route('posts.store'), [
                'title' => 'Test Image Post',
                'slug' => 'test-image-post',
                'post_type' => 'image',
                'status' => 'published',
                'caption' => 'Test caption for image',
                'excerpt' => 'Test excerpt',
                'content' => 'Test content',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('posts', [
            'title' => 'Test Image Post',
            'slug' => 'test-image-post',
            'status' => 'published',
            'postable_type' => ImagePost::class,
        ]);

        $this->assertDatabaseHas('image_posts', [
            'caption' => 'Test caption for image',
        ]);
    });

    it('can store a video post', function () {
        $response = $this->actingAs($this->user)
            ->post(route('posts.store'), [
                'title' => 'Test Video Post',
                'slug' => 'test-video-post',
                'post_type' => 'video',
                'status' => 'published',
                'video_url' => 'https://youtube.com/watch?v=test123',
                'provider' => 'youtube',
                'duration_seconds' => 300,
                'episode_number' => 1,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('posts', [
            'title' => 'Test Video Post',
            'slug' => 'test-video-post',
            'postable_type' => VideoPost::class,
        ]);

        $this->assertDatabaseHas('video_posts', [
            'video_url' => 'https://youtube.com/watch?v=test123',
            'provider' => 'youtube',
            'duration_seconds' => 300,
        ]);
    });

    it('can store an audio post', function () {
        $response = $this->actingAs($this->user)
            ->post(route('posts.store'), [
                'title' => 'Test Audio Post',
                'slug' => 'test-audio-post',
                'post_type' => 'audio',
                'status' => 'draft',
                'audio_url' => 'https://example.com/podcast.mp3',
                'duration_seconds' => 1800,
                'episode_number' => 5,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('posts', [
            'title' => 'Test Audio Post',
            'slug' => 'test-audio-post',
            'status' => 'draft',
            'postable_type' => AudioPost::class,
        ]);

        $this->assertDatabaseHas('audio_posts', [
            'audio_url' => 'https://example.com/podcast.mp3',
            'duration_seconds' => 1800,
            'episode_number' => 5,
        ]);
    });

    it('validates required fields when storing', function () {
        $response = $this->actingAs($this->user)
            ->post(route('posts.store'), []);

        $response->assertSessionHasErrors(['title', 'slug', 'post_type', 'status']);
    });

    it('validates post type enum values', function () {
        $response = $this->actingAs($this->user)
            ->post(route('posts.store'), [
                'title' => 'Test Post',
                'slug' => 'test-post',
                'post_type' => 'invalid_type',
                'status' => 'published',
            ]);

        $response->assertSessionHasErrors(['post_type']);
    });

    it('can show a post', function () {
        $type = fake()->randomElement(PostType::cases());
        $postable = match ($type) {
            PostType::IMAGE => ImagePost::factory()->create(),
            PostType::VIDEO => VideoPost::factory()->create(),
            PostType::AUDIO => AudioPost::factory()->create(),
            PostType::NEWSLETTER => NewsletterPost::factory()->create(),
        };
        $post = Post::factory()->create([
            'postable_type' => get_class($postable),
            'postable_id' => $postable->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('posts.show', $post));

        $response->assertStatus(200);
        $response->assertViewIs('posts.show');
        $response->assertViewHas('post');
    });

    it('can edit a post', function () {
        $type = fake()->randomElement(PostType::cases());
        $postable = match ($type) {
            PostType::IMAGE => ImagePost::factory()->create(),
            PostType::VIDEO => VideoPost::factory()->create(),
            PostType::AUDIO => AudioPost::factory()->create(),
            PostType::NEWSLETTER => NewsletterPost::factory()->create(),
        };
        $post = Post::factory()->create([
            'author_id' => $this->user->id,
            'postable_type' => get_class($postable),
            'postable_id' => $postable->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('posts.edit', $post));

        $response->assertStatus(200);
        $response->assertViewIs('posts.edit');
        $response->assertViewHas('post');
        $response->assertViewHas('postTypes');
        $response->assertViewHas('taxonomyTerms');
    });

    it('can update a post', function () {
        $imagePost = ImagePost::factory()->create();
        $post = Post::factory()->create([
            'author_id' => $this->user->id,
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
            'title' => 'Original Title',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('posts.update', $post), [
                'title' => 'Updated Title',
                'slug' => 'updated-title',
                'post_type' => 'image',
                'status' => 'published',
                'caption' => 'Updated caption',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
            'slug' => 'updated-title',
            'status' => 'published',
        ]);

        $this->assertDatabaseHas('image_posts', [
            'id' => $imagePost->id,
            'caption' => 'Updated caption',
        ]);
    });

    it('can change post type when updating', function () {
        $imagePost = ImagePost::factory()->create();
        $post = Post::factory()->create([
            'author_id' => $this->user->id,
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('posts.update', $post), [
                'title' => $post->title,
                'slug' => $post->slug,
                'post_type' => 'video',
                'status' => $post->status,
                'video_url' => 'https://youtube.com/watch?v=new',
                'provider' => 'youtube',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'postable_type' => VideoPost::class,
        ]);

        $this->assertDatabaseHas('video_posts', [
            'video_url' => 'https://youtube.com/watch?v=new',
            'provider' => 'youtube',
        ]);
    });

    it('can delete a post', function () {
        $type = fake()->randomElement(PostType::cases());
        $postable = match ($type) {
            PostType::IMAGE => ImagePost::factory()->create(),
            PostType::VIDEO => VideoPost::factory()->create(),
            PostType::AUDIO => AudioPost::factory()->create(),
            PostType::NEWSLETTER => NewsletterPost::factory()->create(),
        };
        $post = Post::factory()->create([
            'author_id' => $this->user->id,
            'postable_type' => get_class($postable),
            'postable_id' => $postable->id,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('posts.destroy', $post));

        $response->assertRedirect(route('posts.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
        $this->assertDatabaseMissing(
            match ($type) {
                PostType::IMAGE => 'image_posts',
                PostType::VIDEO => 'video_posts',
                PostType::AUDIO => 'audio_posts',
                PostType::NEWSLETTER => 'newsletter_posts',
            },
            ['id' => $postable->id]
        );
    });

    it('can attach taxonomy terms when storing', function () {
        $response = $this->actingAs($this->user)
            ->post(route('posts.store'), [
                'title' => 'Post with Tags',
                'slug' => 'post-with-tags',
                'post_type' => 'image',
                'status' => 'published',
                'caption' => 'Test caption',
            ]);

        $response->assertRedirect();

        $post = Post::where('slug', 'post-with-tags')->first();
        expect($post)->not->toBeNull();
    });

    it('prevents unauthorized users from creating posts', function () {
        $unauthorizedUser = User::factory()->create();
        // No role assigned

        $response = $this->actingAs($unauthorizedUser)
            ->post(route('posts.store'), [
                'title' => 'Unauthorized Post',
                'slug' => 'unauthorized-post',
                'post_type' => 'image',
                'status' => 'draft',
            ]);

        $response->assertStatus(403);
    });

    it('prevents authors from editing other users posts', function () {
        $authorUser = User::factory()->create();
        $authorUser->assignRole('Author');

        $otherUser = User::factory()->create();
        $otherUser->assignRole('Author');

        $imagePost = ImagePost::factory()->create();
        $post = Post::factory()->create([
            'author_id' => $otherUser->id,
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
        ]);

        $response = $this->actingAs($authorUser)
            ->get(route('posts.edit', $post));

        $response->assertStatus(403);
    });
});
