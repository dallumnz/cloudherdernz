<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any comments.
     */
    public function viewAny(User $user): bool
    {
        return true; // Anyone can view approved comments
    }

    /**
     * Determine whether the user can view the comment.
     */
    public function view(User $user, Comment $comment): bool
    {
        // Anyone can view approved comments
        if ($comment->is_approved) {
            return true;
        }

        // Users can view their own unapproved comments
        if ($comment->user_id === $user->id) {
            return true;
        }

        // Users with moderation permission can view all comments
        return $user->can('moderate comments');
    }

    /**
     * Determine whether the user can create comments.
     */
    public function create(User $user): bool
    {
        // Any authenticated user can create comments
        return true;
    }

    /**
     * Determine whether the user can update the comment.
     */
    public function update(User $user, Comment $comment): bool
    {
        // Users can only update their own comments
        if ($comment->user_id === $user->id) {
            return true;
        }

        // Users with moderation permission can update any comment
        return $user->can('moderate comments');
    }

    /**
     * Determine whether the user can delete the comment.
     */
    public function delete(User $user, Comment $comment): bool
    {
        // Users can only delete their own comments
        if ($comment->user_id === $user->id) {
            return true;
        }

        // Users with moderation permission can delete any comment
        return $user->can('moderate comments');
    }

    /**
     * Determine whether the user can restore the comment.
     */
    public function restore(User $user, Comment $comment): bool
    {
        return $user->can('moderate comments');
    }

    /**
     * Determine whether the user can permanently delete the comment.
     */
    public function forceDelete(User $user, Comment $comment): bool
    {
        return $user->can('moderate comments');
    }
}
