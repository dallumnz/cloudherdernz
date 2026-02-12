<?php

namespace App\Enums;

enum PostType: string
{
    case IMAGE = 'image';
    case VIDEO = 'video';
    case AUDIO = 'audio';
    case NEWSLETTER = 'newsletter';

    public function label(): string
    {
        return match ($this) {
            self::IMAGE => 'Image Post',
            self::VIDEO => 'Video Post',
            self::AUDIO => 'Audio Post',
            self::NEWSLETTER => 'Newsletter Post',
        };
    }

    public function model(): string
    {
        return match ($this) {
            self::IMAGE => \App\Models\ImagePost::class,
            self::VIDEO => \App\Models\VideoPost::class,
            self::AUDIO => \App\Models\AudioPost::class,
            self::NEWSLETTER => \App\Models\NewsletterPost::class,
        };
    }

    public function migration(): string
    {
        return match ($this) {
            self::IMAGE => 'create_image_posts_table',
            self::VIDEO => 'create_video_posts_table',
            self::AUDIO => 'create_audio_posts_table',
            self::NEWSLETTER => 'create_newsletter_posts_table',
        };
    }
}
