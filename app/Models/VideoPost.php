<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\VideoPostFactory;
use Illuminate\Database\Eloquent\Collection;
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
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection<int, Post> $posts
 */
class VideoPost extends Model implements HasMedia
{
    /** @use HasFactory<VideoPostFactory> */
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
     * Get the video file URL.
     *
     * Returns the MediaLibrary URL if a file has been uploaded,
     * otherwise falls back to the stored external URL.
     */
    public function getVideoFileUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('video');

        return $media?->getUrl() ?? $this->video_url;
    }

    /**
     * Get the thumbnail image URL.
     *
     * Returns the MediaLibrary URL if a file has been uploaded,
     * otherwise falls back to the stored external URL.
     */
    public function getThumbnailFileUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('thumbnail');

        return $media?->getUrl() ?? $this->thumbnail_url;
    }

    /**
     * Upload a video file to the media library.
     *
     * @param  string  $path  Absolute path to the uploaded file
     * @param  string|null  $name  Optional custom file name
     */
    public function uploadVideo(string $path, ?string $name = null): void
    {
        $media = $this->addMedia($path)
            ->usingName($name ?? basename($path))
            ->toMediaCollection('video');

        // Clear the external URL since we now have a local file
        if ($media && $this->video_url) {
            $this->update(['video_url' => null]);
        }
    }

    /**
     * Upload a thumbnail image to the media library.
     *
     * @param  string  $path  Absolute path to the uploaded file
     * @param  string|null  $name  Optional custom file name
     */
    public function uploadThumbnail(string $path, ?string $name = null): void
    {
        $this->addMedia($path)
            ->usingName($name ?? basename($path))
            ->toMediaCollection('thumbnail');
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
