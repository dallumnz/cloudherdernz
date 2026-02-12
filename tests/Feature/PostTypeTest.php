<?php

use App\Enums\PostType;
use App\Models\AudioPost;
use App\Models\ImagePost;
use App\Models\NewsletterPost;
use App\Models\Post;
use App\Models\User;
use App\Models\VideoPost;

// Ensure NewsletterPost model is loaded
class_exists(\App\Models\NewsletterPost::class);

describe('Polymorphic Post Type Feature', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
    });

    it('can create image posts', function () {
        $imagePost = ImagePost::factory()->create([
            'caption' => 'Test caption',
        ]);

        $post = Post::factory()->create([
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
        ]);

        $this->assertInstanceOf(ImagePost::class, $post->postable);
        $this->assertEquals('Test caption', $post->postable->caption);
    });

    it('can create video posts', function () {
        $videoPost = VideoPost::factory()->create([
            'video_url' => 'https://youtube.com/watch?v=test',
            'provider' => 'youtube',
        ]);

        $post = Post::factory()->create([
            'postable_type' => VideoPost::class,
            'postable_id' => $videoPost->id,
        ]);

        $this->assertInstanceOf(VideoPost::class, $post->postable);
        $this->assertEquals('youtube', $post->postable->provider);
    });

    it('can create audio posts', function () {
        $audioPost = AudioPost::factory()->create([
            'audio_url' => 'https://example.com/podcast.mp3',
        ]);

        $post = Post::factory()->create([
            'postable_type' => AudioPost::class,
            'postable_id' => $audioPost->id,
        ]);

        $this->assertInstanceOf(AudioPost::class, $post->postable);
    });

    it('post type enum has correct values', function () {
        $this->assertEquals('image', PostType::IMAGE->value);
        $this->assertEquals('video', PostType::VIDEO->value);
        $this->assertEquals('audio', PostType::AUDIO->value);
        $this->assertEquals('newsletter', PostType::NEWSLETTER->value);
    });

    it('post type enum returns correct model class', function () {
        $this->assertEquals(ImagePost::class, PostType::IMAGE->model());
        $this->assertEquals(VideoPost::class, PostType::VIDEO->model());
        $this->assertEquals(AudioPost::class, PostType::AUDIO->model());
        $this->assertEquals(NewsletterPost::class, PostType::NEWSLETTER->model());
    });

    it('post type enum returns correct labels', function () {
        $this->assertEquals('Image Post', PostType::IMAGE->label());
        $this->assertEquals('Video Post', PostType::VIDEO->label());
        $this->assertEquals('Audio Post', PostType::AUDIO->label());
        $this->assertEquals('Newsletter Post', PostType::NEWSLETTER->label());
    });

    it('can create newsletter posts', function () {
        $newsletterPost = NewsletterPost::factory()->create([
            'template' => 'promotional',
        ]);

        $post = Post::factory()->create([
            'postable_type' => NewsletterPost::class,
            'postable_id' => $newsletterPost->id,
        ]);

        $this->assertInstanceOf(NewsletterPost::class, $post->postable);
        $this->assertEquals('promotional', $post->postable->template);
    });

    it('can check if post is of specific type', function () {
        $imagePost = ImagePost::factory()->create();
        $post = Post::factory()->create([
            'postable_type' => ImagePost::class,
            'postable_id' => $imagePost->id,
        ]);

        $this->assertTrue($post->isType(PostType::IMAGE));
        $this->assertFalse($post->isType(PostType::VIDEO));
        $this->assertFalse($post->isType(PostType::AUDIO));
    });
});
