<?php

namespace App\Listeners;

use App\Events\CommentCreated;
use App\Notifications\NewComment;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendNewCommentNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(CommentCreated $event): void
    {
        $comment = $event->comment;
        $commentable = $comment->commentable;

        // Get the author of the commentable entity (e.g., post author)
        $author = $this->getCommentableAuthor($commentable);

        // Don't notify if commenter is the author
        if (! $author || $author->id === $comment->user_id) {
            return;
        }

        // Send notification to the author
        $author->notify(new NewComment($comment));
    }

    /**
     * Get the author of the commentable entity.
     */
    private function getCommentableAuthor($commentable): ?\App\Models\User
    {
        // Handle Post model
        if ($commentable instanceof \App\Models\Post) {
            return $commentable->author;
        }

        // Add more commentable types here as needed
        // if ($commentable instanceof \App\Models\Page) {
        //     return $commentable->author;
        // }

        return null;
    }
}
