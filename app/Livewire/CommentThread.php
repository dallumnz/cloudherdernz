<?php

namespace App\Livewire;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class CommentThread extends Component
{
    #[Locked]
    public Post $post;

    public string $newCommentBody = '';

    public ?int $replyingTo = null;

    public string $replyBody = '';

    public bool $showUnapproved = false;

    /**
     * Mount the component with the given post.
     */
    public function mount(Post $post): void
    {
        $this->post = $post;
        $this->showUnapproved = Auth::check() && Auth::user()->can('moderate comments');
    }

    /**
     * Get comments for the post.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Comment>
     */
    public function getCommentsProperty()
    {
        $query = $this->post->comments()
            ->with(['user', 'children.user'])
            ->whereNull('parent_id');

        if (! $this->showUnapproved) {
            $query->where('is_approved', true);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Check if the current user is an admin.
     */
    public function getIsAdminProperty(): bool
    {
        return Auth::check() && Auth::user()->can('moderate comments');
    }

    /**
     * Post a new top-level comment.
     */
    public function postComment(): void
    {
        Gate::authorize('create', Comment::class);

        $this->validate([
            'newCommentBody' => ['required', 'string', 'min:1', 'max:5000'],
        ], [
            'newCommentBody.required' => 'Please enter a comment.',
            'newCommentBody.min' => 'Your comment is too short.',
            'newCommentBody.max' => 'Your comment is too long (max 5000 characters).',
        ]);

        $comment = new Comment([
            'user_id' => Auth::id(),
            'body' => $this->newCommentBody,
            'is_approved' => $this->isAdmin,
        ]);

        $this->post->comments()->save($comment);

        $this->newCommentBody = '';

        $this->dispatch('comment-posted');

        if (! $comment->is_approved) {
            $this->dispatch('notify', [
                'type' => 'info',
                'message' => 'Your comment has been submitted and is pending approval.',
            ]);
        }
    }

    /**
     * Start replying to a specific comment.
     */
    public function startReply(int $commentId): void
    {
        Gate::authorize('create', Comment::class);
        $this->replyingTo = $commentId;
        $this->replyBody = '';
    }

    /**
     * Cancel the current reply.
     */
    public function cancelReply(): void
    {
        $this->replyingTo = null;
        $this->replyBody = '';
    }

    /**
     * Post a reply to a comment.
     */
    public function postReply(): void
    {
        Gate::authorize('create', Comment::class);

        if ($this->replyingTo === null) {
            return;
        }

        $this->validate([
            'replyBody' => ['required', 'string', 'min:1', 'max:5000'],
        ], [
            'replyBody.required' => 'Please enter a reply.',
            'replyBody.min' => 'Your reply is too short.',
            'replyBody.max' => 'Your reply is too long (max 5000 characters).',
        ]);

        $parentComment = Comment::find($this->replyingTo);

        if (! $parentComment) {
            return;
        }

        $comment = new Comment([
            'user_id' => Auth::id(),
            'body' => $this->replyBody,
            'parent_id' => $this->replyingTo,
            'is_approved' => $this->isAdmin,
        ]);

        $this->post->comments()->save($comment);

        $this->replyingTo = null;
        $this->replyBody = '';

        $this->dispatch('comment-posted');

        if (! $comment->is_approved) {
            $this->dispatch('notify', [
                'type' => 'info',
                'message' => 'Your reply has been submitted and is pending approval.',
            ]);
        }
    }

    /**
     * Approve a comment (admin only).
     */
    public function approveComment(int $commentId): void
    {
        $comment = Comment::findOrFail($commentId);
        Gate::authorize('update', $comment);

        $comment->approve();

        $this->dispatch('comment-approved');
    }

    /**
     * Delete a comment (soft delete).
     */
    public function deleteComment(int $commentId): void
    {
        $comment = Comment::findOrFail($commentId);
        Gate::authorize('delete', $comment);

        $comment->delete();

        $this->dispatch('comment-deleted');
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('livewire.comment-thread', [
            'comments' => $this->comments,
            'isAdmin' => $this->isAdmin,
        ]);
    }
}
