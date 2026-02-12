<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Audio Post Model
 *
 * Represents an audio-based post type (e.g., podcasts). Stores audio-specific
 * data like URLs, duration, and episode information. Uses polymorphic
 * relationship to connect with the main Post model.
 *
 * @property int $id
 * @property string|null $audio_url
 * @property int|null $duration_seconds
 * @property int|null $episode_number
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Post> $posts
 */
class AudioPost extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\AudioPostFactory> */
    use HasFactory;

    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'audio_url',
        'duration_seconds',
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
     * Get all posts that belong to this audio post.
     *
     * @return MorphMany<Post, $this>
     */
    public function posts(): MorphMany
    {
        return $this->morphMany(Post::class, 'postable');
    }

    /**
     * Register media collections for the audio post.
     *
     * Defines the audio collection for podcast/audio file uploads.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('audio')
            ->acceptsMimeTypes(['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/mp3']);
    }
}
