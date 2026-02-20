<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Comment API', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->post = Post::factory()->published()->create();
    });

    describe('index', function () {
        it('lists approved comments for a commentable', function () {
            Comment::factory()->count(3)->approved()->on(Post::class, $this->post->id)->create();
            Comment::factory()->count(2)->unapproved()->on(Post::class, $this->post->id)->create();

            $response = $this->actingAs($this->user)
                ->getJson("/api/v1/posts/{$this->post->id}/comments");

            $response->assertStatus(200)
                ->assertJsonCount(3, 'data');
        });

        it('returns paginated results', function () {
            Comment::factory()->count(20)->approved()->on(Post::class, $this->post->id)->create();

            $response = $this->actingAs($this->user)
                ->getJson("/api/v1/posts/{$this->post->id}/comments?per_page=10");

            $response->assertStatus(200)
                ->assertJsonCount(10, 'data')
                ->assertJsonPath('meta.per_page', 10);
        });

        it('returns 404 for non-existent commentable', function () {
            $response = $this->actingAs($this->user)
                ->getJson('/api/v1/posts/99999/comments');

            $response->assertStatus(404);
        });
    });

    describe('store', function () {
        it('creates a comment on a post', function () {
            $response = $this->actingAs($this->user)
                ->postJson("/api/v1/posts/{$this->post->id}/comments", [
                    'body' => 'This is a test comment.',
                ]);

            $response->assertStatus(201)
                ->assertJsonPath('data.body', 'This is a test comment.')
                ->assertJsonPath('data.user.id', $this->user->id);

            $this->assertDatabaseHas('comments', [
                'body' => 'This is a test comment.',
                'user_id' => $this->user->id,
                'commentable_type' => Post::class,
                'commentable_id' => $this->post->id,
            ]);
        });

        it('validates body is required', function () {
            $response = $this->actingAs($this->user)
                ->postJson("/api/v1/posts/{$this->post->id}/comments", [
                    'body' => '',
                ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['body']);
        });

        it('validates body max length', function () {
            $response = $this->actingAs($this->user)
                ->postJson("/api/v1/posts/{$this->post->id}/comments", [
                    'body' => str_repeat('a', 5001),
                ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['body']);
        });

        it('creates a reply to another comment', function () {
            $parentComment = Comment::factory()->approved()->on(Post::class, $this->post->id)->create();

            $response = $this->actingAs($this->user)
                ->postJson("/api/v1/posts/{$this->post->id}/comments", [
                    'body' => 'This is a reply.',
                    'parent_id' => $parentComment->id,
                ]);

            $response->assertStatus(201)
                ->assertJsonPath('data.parent_id', $parentComment->id);
        });

        it('returns 422 for invalid parent_id', function () {
            $response = $this->actingAs($this->user)
                ->postJson("/api/v1/posts/{$this->post->id}/comments", [
                    'body' => 'This is a reply.',
                    'parent_id' => 99999,
                ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['parent_id']);
        });

        it('requires authentication', function () {
            $response = $this->postJson("/api/v1/posts/{$this->post->id}/comments", [
                'body' => 'This is a test comment.',
            ]);

            $response->assertStatus(401);
        });
    });

    describe('show', function () {
        it('shows an approved comment', function () {
            $comment = Comment::factory()->approved()->on(Post::class, $this->post->id)->create();

            $response = $this->actingAs($this->user)
                ->getJson("/api/v1/comments/{$comment->id}");

            $response->assertStatus(200)
                ->assertJsonPath('data.id', $comment->id)
                ->assertJsonPath('data.body', $comment->body);
        });

        it('hides unapproved comments from non-moderators', function () {
            $comment = Comment::factory()->unapproved()->on(Post::class, $this->post->id)->create();

            $response = $this->actingAs($this->user)
                ->getJson("/api/v1/comments/{$comment->id}");

            $response->assertStatus(403);
        });
    });

    describe('update', function () {
        it('allows users to update their own comments', function () {
            $comment = Comment::factory()->by($this->user)->on(Post::class, $this->post->id)->create();

            $response = $this->actingAs($this->user)
                ->putJson("/api/v1/comments/{$comment->id}", [
                    'body' => 'Updated comment body.',
                ]);

            $response->assertStatus(200)
                ->assertJsonPath('data.body', 'Updated comment body.');

            $this->assertDatabaseHas('comments', [
                'id' => $comment->id,
                'body' => 'Updated comment body.',
            ]);
        });

        it('denies updating comments from other users', function () {
            $otherUser = User::factory()->create();
            $comment = Comment::factory()->by($otherUser)->on(Post::class, $this->post->id)->create();

            $response = $this->actingAs($this->user)
                ->putJson("/api/v1/comments/{$comment->id}", [
                    'body' => 'Updated comment body.',
                ]);

            $response->assertStatus(403);
        });

        it('validates body is required', function () {
            $comment = Comment::factory()->by($this->user)->on(Post::class, $this->post->id)->create();

            $response = $this->actingAs($this->user)
                ->putJson("/api/v1/comments/{$comment->id}", [
                    'body' => '',
                ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['body']);
        });
    });

    describe('destroy', function () {
        it('allows users to delete their own comments', function () {
            $comment = Comment::factory()->by($this->user)->on(Post::class, $this->post->id)->create();

            $response = $this->actingAs($this->user)
                ->deleteJson("/api/v1/comments/{$comment->id}");

            $response->assertStatus(200)
                ->assertJsonPath('message', 'Comment deleted successfully.');

            $this->assertSoftDeleted('comments', [
                'id' => $comment->id,
            ]);
        });

        it('denies deleting comments from other users', function () {
            $otherUser = User::factory()->create();
            $comment = Comment::factory()->by($otherUser)->on(Post::class, $this->post->id)->create();

            $response = $this->actingAs($this->user)
                ->deleteJson("/api/v1/comments/{$comment->id}");

            $response->assertStatus(403);
        });
    });
});
