<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewComment extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Comment $comment) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $commentable = $this->comment->commentable;
        $commenter = $this->comment->user;

        $title = $this->getCommentableTitle($commentable);
        $url = $this->getCommentableUrl($commentable);

        return (new MailMessage)
            ->subject('New Comment on '.$title)
            ->greeting('Hello '.$notifiable->name.'!')
            ->line($commenter->name.' has left a new comment on "'.$title.'":')
            ->line('"'.str($this->comment->body)->limit(100).'"')
            ->action('View Comment', $url)
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $commentable = $this->comment->commentable;

        return [
            'comment_id' => $this->comment->id,
            'comment_body' => $this->comment->body,
            'commenter_id' => $this->comment->user_id,
            'commenter_name' => $this->comment->user->name,
            'commentable_type' => $this->comment->commentable_type,
            'commentable_id' => $this->comment->commentable_id,
            'commentable_title' => $this->getCommentableTitle($commentable),
            'url' => $this->getCommentableUrl($commentable),
        ];
    }

    /**
     * Get the title of the commentable entity.
     */
    private function getCommentableTitle($commentable): string
    {
        if ($commentable instanceof \App\Models\Post) {
            return $commentable->title;
        }

        return 'your content';
    }

    /**
     * Get the URL of the commentable entity.
     */
    private function getCommentableUrl($commentable): string
    {
        if ($commentable instanceof \App\Models\Post) {
            return route('api.posts.show', $commentable);
        }

        return url('/');
    }
}
