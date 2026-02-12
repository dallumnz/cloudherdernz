<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Image Post Model
 *
 * Represents an image-based post type. Stores image-specific data
 * like captions and gallery settings. Uses polymorphic relationship
 * to connect with the main Post model.
 *
 * @property int $id
 * @property string|null $caption
 * @property array|null $gallery_settings
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Post> $posts
 */
class ImagePost extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\ImagePostFactory> */
    use HasFactory;

    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'caption',
        'gallery_settings',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'gallery_settings' => 'array',
    ];

    /**
     * Get all posts that belong to this image post.
     *
     * @return MorphMany<Post, $this>
     */
    public function posts(): MorphMany
    {
        return $this->morphMany(Post::class, 'postable');
    }

    /**
     * Register media collections for the image post.
     *
     * Defines the gallery collection with responsive images support.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->withResponsiveImages();
    }
}
