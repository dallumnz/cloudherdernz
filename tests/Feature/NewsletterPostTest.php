<?php

use App\Enums\PostType;
use App\Models\NewsletterPost;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

describe('Newsletter Post Feature', function () {
    beforeEach(function () {
        $this->seed(RolePermissionSeeder::class);
        $this->user = User::factory()->create();
        $this->user->givePermissionTo('create posts', 'edit posts', 'delete posts', 'view posts');
    });

    it('can create newsletter posts', function () {
        $newsletterPost = NewsletterPost::factory()->create([
            'template' => 'promotional',
            'subscriber_settings' => ['send_to_all' => true],
        ]);

        $post = Post::factory()->create([
            'postable_type' => NewsletterPost::class,
            'postable_id' => $newsletterPost->id,
        ]);

        expect($post->postable)->toBeInstanceOf(NewsletterPost::class);
        expect($post->postable->template)->toBe('promotional');
        expect($post->postable->subscriber_settings)->toBe(['send_to_all' => true]);
    });

    it('post type enum includes newsletter', function () {
        expect(PostType::NEWSLETTER->value)->toBe('newsletter');
        expect(PostType::NEWSLETTER->label())->toBe('Newsletter Post');
        expect(PostType::NEWSLETTER->model())->toBe(NewsletterPost::class);
    });

    it('can check if post is newsletter type', function () {
        $newsletterPost = NewsletterPost::factory()->create();
        $post = Post::factory()->create([
            'postable_type' => NewsletterPost::class,
            'postable_id' => $newsletterPost->id,
        ]);

        expect($post->isType(PostType::NEWSLETTER))->toBeTrue();
        expect($post->isType(PostType::IMAGE))->toBeFalse();
        expect($post->isType(PostType::VIDEO))->toBeFalse();
    });

    it('newsletter post has correct default values', function () {
        $newsletterPost = NewsletterPost::factory()->create();

        expect($newsletterPost->is_sent)->toBeFalse();
        expect($newsletterPost->opens_count)->toBe(0);
        expect($newsletterPost->clicks_count)->toBe(0);
    });

    it('can mark newsletter as sent', function () {
        $newsletterPost = NewsletterPost::factory()->create();

        $newsletterPost->markAsSent(1000);

        expect($newsletterPost->is_sent)->toBeTrue();
        expect($newsletterPost->sent_at)->not->toBeNull();
        expect($newsletterPost->recipients_count)->toBe(1000);
    });

    it('can record opens and clicks', function () {
        $newsletterPost = NewsletterPost::factory()->sent()->create();

        $newsletterPost->recordOpen();
        $newsletterPost->recordClick();

        expect($newsletterPost->opens_count)->toBe(1);
        expect($newsletterPost->clicks_count)->toBe(1);
    });

    it('calculates open rate correctly', function () {
        $newsletterPost = NewsletterPost::factory()->create([
            'is_sent' => true,
            'recipients_count' => 100,
            'opens_count' => 25,
        ]);

        expect($newsletterPost->open_rate)->toBe(25.0);
    });

    it('calculates click rate correctly', function () {
        $newsletterPost = NewsletterPost::factory()->create([
            'is_sent' => true,
            'recipients_count' => 100,
            'clicks_count' => 10,
        ]);

        expect($newsletterPost->click_rate)->toBe(10.0);
    });

    it('returns null rates when no recipients', function () {
        $newsletterPost = NewsletterPost::factory()->create([
            'recipients_count' => null,
        ]);

        expect($newsletterPost->open_rate)->toBeNull();
        expect($newsletterPost->click_rate)->toBeNull();
    });

    it('factory can create sent newsletters', function () {
        $newsletterPost = NewsletterPost::factory()->sent()->create();

        expect($newsletterPost->is_sent)->toBeTrue();
        expect($newsletterPost->sent_at)->not->toBeNull();
        expect($newsletterPost->recipients_count)->toBeGreaterThan(0);
    });

    it('factory can create newsletters with engagement', function () {
        $newsletterPost = NewsletterPost::factory()->withEngagement()->create();

        expect($newsletterPost->is_sent)->toBeTrue();
        expect($newsletterPost->opens_count)->toBeGreaterThanOrEqual(0);
        expect($newsletterPost->clicks_count)->toBeGreaterThanOrEqual(0);
    });

    it('post factory has newsletter state', function () {
        $post = Post::factory()->newsletter()->create([
            'postable_id' => NewsletterPost::factory()->create()->id,
        ]);

        expect($post->postable_type)->toBe(NewsletterPost::class);
        expect($post->isType(PostType::NEWSLETTER))->toBeTrue();
    });

    it('has polymorphic relationship with posts', function () {
        $newsletterPost = NewsletterPost::factory()->create();
        $post1 = Post::factory()->create([
            'postable_type' => NewsletterPost::class,
            'postable_id' => $newsletterPost->id,
        ]);
        $post2 = Post::factory()->create([
            'postable_type' => NewsletterPost::class,
            'postable_id' => $newsletterPost->id,
        ]);

        expect($newsletterPost->posts)->toHaveCount(2);
        expect($newsletterPost->posts->first()->id)->toBe($post1->id);
    });
});
