<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Video Post Model
 *
 * Represents a video-based post type. Stores video-specific data
 * like URLs, duration, provider, and episode information. Uses
 * polymorphic relationship to connect with the main Post model.
 *
 * @property int $id
 * @property string|null $video_url
 * @property string|null $thumbnail_url
 * @property int|null $duration_seconds
 * @property string|null $provider
 * @property int|null $episode_number
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Post> $posts
 */
class VideoPost extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\VideoPostFactory> */
    use HasFactory;

    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'video_url',
        'thumbnail_url',
        'duration_seconds',
        'provider',
        'episode_number',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'duration_seconds' => 'integer',
        'episode_number' => 'integer',
    ];

    /**
     * Get all posts that belong to this video post.
     *
     * @return MorphMany<Post, $this>
     */
    public function posts(): MorphMany
    {
        return $this->morphMany(Post::class, 'postable');
    }

    /**
     * Register media collections for the video post.
     *
     * Defines collections:
     * - video: Video file uploads
     * - thumbnail: Single thumbnail image
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('video')
            ->acceptsMimeTypes(['video/mp4', 'video/webm', 'video/quicktime']);

        $this->addMediaCollection('thumbnail')
            ->singleFile();
    }
}
