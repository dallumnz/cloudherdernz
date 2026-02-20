<?php

use App\Events\CommentCreated;
use App\Listeners\SendNewCommentNotification;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Notifications\NewComment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

describe('Comment Notifications', function () {
    beforeEach(function () {
        $this->author = User::factory()->create();
        $this->post = Post::factory()->published()->create([
            'author_id' => $this->author->id,
        ]);
    });

    describe('CommentCreated Event', function () {
        it('fires CommentCreated event when approved comment is created', function () {
            // Create the Admin role and permission if they don't exist
            $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
            $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'moderate comments', 'guard_name' => 'web']);
            $role->givePermissionTo($permission);

            // Create an admin user who can create approved comments
            $admin = User::factory()->create();
            $admin->assignRole('Admin');

            // Verify the user has the permission
            expect($admin->can('moderate comments'))->toBeTrue();

            Event::fake([CommentCreated::class]);

            $response = $this->actingAs($admin, 'sanctum')
                ->postJson("/api/v1/posts/{$this->post->id}/comments", [
                    'body' => 'Test comment.',
                ]);

            $response->assertStatus(201);

            // Debug: Check the response
            $data = $response->json();
            expect($data['data']['is_approved'])->toBeTrue();

            Event::assertDispatched(CommentCreated::class, function ($event) {
                return $event->comment->body === 'Test comment.';
            });
        });

        it('does not fire event for unapproved comments', function () {
            Event::fake([CommentCreated::class]);

            $user = User::factory()->create();

            // Regular users create unapproved comments by default
            $this->actingAs($user)
                ->postJson("/api/v1/posts/{$this->post->id}/comments", [
                    'body' => 'Test comment.',
                ]);

            // Event should NOT be dispatched since comment is unapproved
            Event::assertNotDispatched(CommentCreated::class);
        });
    });

    describe('SendNewCommentNotification Listener', function () {
        it('sends notification to post author when comment is created', function () {
            Notification::fake();

            $commenter = User::factory()->create();
            $comment = Comment::factory()->approved()->by($commenter)->on(Post::class, $this->post->id)->create();

            $listener = new SendNewCommentNotification;
            $listener->handle(new CommentCreated($comment));

            Notification::assertSentTo(
                $this->author,
                NewComment::class,
                function ($notification) use ($comment) {
                    return $notification->comment->id === $comment->id;
                }
            );
        });

        it('does not send notification when commenter is the author', function () {
            Notification::fake();

            // Author comments on their own post
            $comment = Comment::factory()->approved()->by($this->author)->on(Post::class, $this->post->id)->create();

            $listener = new SendNewCommentNotification;
            $listener->handle(new CommentCreated($comment));

            Notification::assertNothingSent();
        });
    });

    describe('NewComment Notification', function () {
        it('sends mail notification', function () {
            $commenter = User::factory()->create();
            $comment = Comment::factory()->approved()->by($commenter)->on(Post::class, $this->post->id)->create();

            $notification = new NewComment($comment);
            $mailMessage = $notification->toMail($this->author);

            expect($mailMessage)->toBeInstanceOf(\Illuminate\Notifications\Messages\MailMessage::class);
            expect($mailMessage->subject)->toContain('New Comment');
        });

        it('sends database notification', function () {
            $commenter = User::factory()->create();
            $comment = Comment::factory()->approved()->by($commenter)->on(Post::class, $this->post->id)->create();

            $notification = new NewComment($comment);
            $arrayData = $notification->toArray($this->author);

            expect($arrayData)->toHaveKey('comment_id');
            expect($arrayData)->toHaveKey('commenter_name');
            expect($arrayData)->toHaveKey('commentable_title');
            expect($arrayData['comment_id'])->toBe($comment->id);
        });
    });
});
