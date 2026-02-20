<?php

use App\Models\Comment;
use App\Models\User;
use App\Policies\CommentPolicy;
use Illuminate\Support\Facades\Gate;

beforeEach(function () {
    $this->policy = new CommentPolicy;
});

describe('CommentPolicy', function () {
    describe('viewAny', function () {
        it('allows anyone to view comments', function () {
            $user = User::factory()->make();

            expect($this->policy->viewAny($user))->toBeTrue();
        });
    });

    describe('view', function () {
        it('allows anyone to view approved comments', function () {
            $user = User::factory()->make();
            $comment = Comment::factory()->approved()->make();

            expect($this->policy->view($user, $comment))->toBeTrue();
        });

        it('allows users to view their own unapproved comments', function () {
            $user = User::factory()->make(['id' => 1]);
            $comment = Comment::factory()->unapproved()->make([
                'user_id' => 1,
            ]);

            expect($this->policy->view($user, $comment))->toBeTrue();
        });

        it('denies viewing unapproved comments from other users', function () {
            $user = User::factory()->make(['id' => 1]);
            $comment = Comment::factory()->unapproved()->make([
                'user_id' => 2,
            ]);

            expect($this->policy->view($user, $comment))->toBeFalse();
        });
    });

    describe('create', function () {
        it('allows any authenticated user to create comments', function () {
            $user = User::factory()->make();

            expect($this->policy->create($user))->toBeTrue();
        });
    });

    describe('update', function () {
        it('allows users to update their own comments', function () {
            $user = User::factory()->make(['id' => 1]);
            $comment = Comment::factory()->make([
                'user_id' => 1,
            ]);

            expect($this->policy->update($user, $comment))->toBeTrue();
        });

        it('denies updating comments from other users', function () {
            $user = User::factory()->make(['id' => 1]);
            $comment = Comment::factory()->make([
                'user_id' => 2,
            ]);

            expect($this->policy->update($user, $comment))->toBeFalse();
        });
    });

    describe('delete', function () {
        it('allows users to delete their own comments', function () {
            $user = User::factory()->make(['id' => 1]);
            $comment = Comment::factory()->make([
                'user_id' => 1,
            ]);

            expect($this->policy->delete($user, $comment))->toBeTrue();
        });

        it('denies deleting comments from other users', function () {
            $user = User::factory()->make(['id' => 1]);
            $comment = Comment::factory()->make([
                'user_id' => 2,
            ]);

            expect($this->policy->delete($user, $comment))->toBeFalse();
        });
    });

    describe('admin permissions', function () {
        it('allows admins to perform any action', function () {
            // Create the Admin role if it doesn't exist
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);

            $admin = User::factory()->create();
            $admin->assignRole('Admin');
            $comment = Comment::factory()->make([
                'user_id' => 999,
            ]);

            // Use Gate to properly test the before() method
            Gate::policy(Comment::class, CommentPolicy::class);

            expect(Gate::forUser($admin)->allows('view', $comment))->toBeTrue();
            expect(Gate::forUser($admin)->allows('update', $comment))->toBeTrue();
            expect(Gate::forUser($admin)->allows('delete', $comment))->toBeTrue();
        });
    });
});
