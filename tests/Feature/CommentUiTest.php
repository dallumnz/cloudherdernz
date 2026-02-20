<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

describe('Comment UI', function () {
    beforeEach(function () {
        $this->seed(RolePermissionSeeder::class);
        $this->user = User::factory()->create();
        $this->post = Post::factory()->published()->create();
    });

    describe('component rendering', function () {
        it('renders the comment thread component', function () {
            Livewire::test('comment-thread', ['post' => $this->post])
                ->assertStatus(200)
                ->assertSee('Comments');
        });

        it('displays approved comments', function () {
            $comment = Comment::factory()
                ->approved()
                ->on(Post::class, $this->post->id)
                ->by($this->user)
                ->create();

            Livewire::test('comment-thread', ['post' => $this->post])
                ->assertSee($comment->body)
                ->assertSee($this->user->name);
        });

        it('hides unapproved comments from non-moderators', function () {
            $comment = Comment::factory()
                ->unapproved()
                ->on(Post::class, $this->post->id)
                ->by($this->user)
                ->create();

            Livewire::test('comment-thread', ['post' => $this->post])
                ->assertDontSee($comment->body);
        });

        it('shows unapproved comments to moderators', function () {
            $admin = User::factory()->create();
            $admin->assignRole('Admin');

            $comment = Comment::factory()
                ->unapproved()
                ->on(Post::class, $this->post->id)
                ->by($this->user)
                ->create();

            Livewire::actingAs($admin)
                ->test('comment-thread', ['post' => $this->post])
                ->assertSee($comment->body)
                ->assertSee('Pending Approval');
        });
    });

    describe('posting comments', function () {
        it('allows authenticated users to post comments', function () {
            Livewire::actingAs($this->user)
                ->test('comment-thread', ['post' => $this->post])
                ->set('newCommentBody', 'This is my test comment.')
                ->call('postComment')
                ->assertDispatched('comment-posted');

            $this->assertDatabaseHas('comments', [
                'body' => 'This is my test comment.',
                'user_id' => $this->user->id,
                'commentable_type' => Post::class,
                'commentable_id' => $this->post->id,
                'parent_id' => null,
            ]);
        });

        it('validates comment body is required', function () {
            Livewire::actingAs($this->user)
                ->test('comment-thread', ['post' => $this->post])
                ->set('newCommentBody', '')
                ->call('postComment')
                ->assertHasErrors(['newCommentBody' => 'required']);
        });

        it('validates comment body max length', function () {
            Livewire::actingAs($this->user)
                ->test('comment-thread', ['post' => $this->post])
                ->set('newCommentBody', str_repeat('a', 5001))
                ->call('postComment')
                ->assertHasErrors(['newCommentBody']);
        });

        it('clears comment body after posting', function () {
            Livewire::actingAs($this->user)
                ->test('comment-thread', ['post' => $this->post])
                ->set('newCommentBody', 'Test comment')
                ->call('postComment')
                ->assertSet('newCommentBody', '');
        });

        it('auto-approves comments from admins', function () {
            $admin = User::factory()->create();
            $admin->assignRole('Admin');

            Livewire::actingAs($admin)
                ->test('comment-thread', ['post' => $this->post])
                ->set('newCommentBody', 'Admin comment')
                ->call('postComment');

            $this->assertDatabaseHas('comments', [
                'body' => 'Admin comment',
                'is_approved' => true,
            ]);
        });

        it('sets non-admin comments as pending', function () {
            Livewire::actingAs($this->user)
                ->test('comment-thread', ['post' => $this->post])
                ->set('newCommentBody', 'Pending comment')
                ->call('postComment')
                ->assertDispatched('notify');

            $this->assertDatabaseHas('comments', [
                'body' => 'Pending comment',
                'is_approved' => false,
            ]);
        });
    });

    describe('replies', function () {
        it('allows users to reply to comments', function () {
            $parentComment = Comment::factory()
                ->approved()
                ->on(Post::class, $this->post->id)
                ->create();

            Livewire::actingAs($this->user)
                ->test('comment-thread', ['post' => $this->post])
                ->call('startReply', $parentComment->id)
                ->assertSet('replyingTo', $parentComment->id)
                ->set('replyBody', 'This is a reply.')
                ->call('postReply')
                ->assertDispatched('comment-posted');

            $this->assertDatabaseHas('comments', [
                'body' => 'This is a reply.',
                'parent_id' => $parentComment->id,
            ]);
        });

        it('validates reply body is required', function () {
            $parentComment = Comment::factory()
                ->approved()
                ->on(Post::class, $this->post->id)
                ->create();

            Livewire::actingAs($this->user)
                ->test('comment-thread', ['post' => $this->post])
                ->call('startReply', $parentComment->id)
                ->set('replyBody', '')
                ->call('postReply')
                ->assertHasErrors(['replyBody' => 'required']);
        });

        it('clears reply state after posting', function () {
            $parentComment = Comment::factory()
                ->approved()
                ->on(Post::class, $this->post->id)
                ->create();

            Livewire::actingAs($this->user)
                ->test('comment-thread', ['post' => $this->post])
                ->call('startReply', $parentComment->id)
                ->set('replyBody', 'Reply text')
                ->call('postReply')
                ->assertSet('replyingTo', null)
                ->assertSet('replyBody', '');
        });

        it('allows canceling a reply', function () {
            $parentComment = Comment::factory()
                ->approved()
                ->on(Post::class, $this->post->id)
                ->create();

            Livewire::actingAs($this->user)
                ->test('comment-thread', ['post' => $this->post])
                ->call('startReply', $parentComment->id)
                ->set('replyBody', 'Draft reply')
                ->call('cancelReply')
                ->assertSet('replyingTo', null)
                ->assertSet('replyBody', '');
        });
    });

    describe('admin moderation', function () {
        beforeEach(function () {
            $this->admin = User::factory()->create();
            $this->admin->assignRole('Admin');
        });

        it('allows admins to approve comments', function () {
            $comment = Comment::factory()
                ->unapproved()
                ->on(Post::class, $this->post->id)
                ->create();

            Livewire::actingAs($this->admin)
                ->test('comment-thread', ['post' => $this->post])
                ->call('approveComment', $comment->id)
                ->assertDispatched('comment-approved');

            $this->assertDatabaseHas('comments', [
                'id' => $comment->id,
                'is_approved' => true,
            ]);
        });

        it('allows admins to delete any comment', function () {
            $comment = Comment::factory()
                ->approved()
                ->on(Post::class, $this->post->id)
                ->by($this->user)
                ->create();

            Livewire::actingAs($this->admin)
                ->test('comment-thread', ['post' => $this->post])
                ->call('deleteComment', $comment->id)
                ->assertDispatched('comment-deleted');

            $this->assertSoftDeleted('comments', [
                'id' => $comment->id,
            ]);
        });

        it('shows moderator badge to admins', function () {
            Livewire::actingAs($this->admin)
                ->test('comment-thread', ['post' => $this->post])
                ->assertSee('Moderator View');
        });
    });

    describe('user comment management', function () {
        it('allows users to delete their own comments', function () {
            $comment = Comment::factory()
                ->approved()
                ->on(Post::class, $this->post->id)
                ->by($this->user)
                ->create();

            Livewire::actingAs($this->user)
                ->test('comment-thread', ['post' => $this->post])
                ->call('deleteComment', $comment->id)
                ->assertDispatched('comment-deleted');

            $this->assertSoftDeleted('comments', [
                'id' => $comment->id,
            ]);
        });

        it('prevents users from deleting other users comments', function () {
            $otherUser = User::factory()->create();
            $comment = Comment::factory()
                ->approved()
                ->on(Post::class, $this->post->id)
                ->by($otherUser)
                ->create();

            Livewire::actingAs($this->user)
                ->test('comment-thread', ['post' => $this->post])
                ->call('deleteComment', $comment->id)
                ->assertForbidden();
        });
    });

    describe('nested display', function () {
        it('displays nested replies', function () {
            $parentComment = Comment::factory()
                ->approved()
                ->on(Post::class, $this->post->id)
                ->create();

            $reply = Comment::factory()
                ->approved()
                ->on(Post::class, $this->post->id)
                ->reply($parentComment->id)
                ->create();

            Livewire::test('comment-thread', ['post' => $this->post])
                ->assertSee($parentComment->body)
                ->assertSee($reply->body);
        });

        it('shows reply count correctly', function () {
            Comment::factory()
                ->count(3)
                ->approved()
                ->on(Post::class, $this->post->id)
                ->create();

            Livewire::test('comment-thread', ['post' => $this->post])
                ->assertSee('Comments (3)');
        });
    });

    describe('guest users', function () {
        it('shows sign in prompt for guests', function () {
            Livewire::test('comment-thread', ['post' => $this->post])
                ->assertSee('sign in')
                ->assertSee('to leave a comment');
        });

        it('prevents guests from posting comments', function () {
            Livewire::test('comment-thread', ['post' => $this->post])
                ->set('newCommentBody', 'Guest comment')
                ->call('postComment')
                ->assertForbidden();
        });
    });
});
