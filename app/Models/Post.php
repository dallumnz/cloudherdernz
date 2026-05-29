<?php

namespace App\Models;

use App\Enums\PostType as PostTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Cache;
use Laravel\Scout\Searchable;
use League\CommonMark\CommonMarkConverter;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * Post Model
 *
 * Represents a content post in the system. Posts can be of different types
 * (image, video, audio) through polymorphic relationships with ImagePost,
 * VideoPost, and AudioPost models.
 *
 * @property int $id
 * @property int $author_id
 * @property string $title
 * @property string $slug
 * @property string|null $excerpt
 * @property string|null $content
 * @property array|null $metadata
 * @property string $status
 * @property \Carbon\Carbon|null $published_at
 * @property string $postable_type
 * @property int $postable_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read PostTypeEnum|null $post_type
 * @property-read User $author
 * @property-read Model $postable
 * @property-read \Illuminate\Database\Eloquent\Collection<int, TaxonomyTerm> $taxonomyTerms
 */
class Post extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;
    use HasSEO;
    use InteractsWithMedia;
    use LogsActivity;
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'metadata',
        'status',
        'published_at',
        'postable_type',
        'postable_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    /**
     * Intercept published_at to store UTC in the database while
     * presenting/applying the application's configured timezone.
     */
    protected function publishedAt(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function (?string $value): ?\Illuminate\Support\Carbon {
                if (blank($value)) {
                    return null;
                }

                return \Illuminate\Support\Carbon::parse($value, 'UTC')
                    ->setTimezone(config('app.timezone'));
            },
            set: function ($value): ?string {
                if (blank($value)) {
                    return null;
                }

                if ($value instanceof \DateTimeInterface) {
                    return $value->setTimezone('UTC')->format('Y-m-d H:i:s');
                }

                return \Illuminate\Support\Carbon::parse($value, config('app.timezone'))
                    ->setTimezone('UTC')
                    ->format('Y-m-d H:i:s');
            }
        );
    }

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'slug', 'status', 'published_at', 'author_id'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    /**
     * Bootstrap the model and its traits.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Auto-generate slug from title if empty
        static::creating(function (Post $post) {
            if (empty($post->slug)) {
                $post->slug = \Illuminate\Support\Str::slug($post->title);
            }
            
            // Set published_at time to current time if only date provided
            if ($post->published_at && $post->published_at->format('H:i:s') === '00:00:00') {
                $post->published_at = $post->published_at->setTimeFromTimeString(now()->format('H:i:s'));
            }
        });

        static::updating(function (Post $post) {
            // Auto-generate slug from title if empty
            if (empty($post->slug)) {
                $post->slug = \Illuminate\Support\Str::slug($post->title);
            }
        });
    }

    /**
     * Get the route key for the model (use slug instead of id).
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the postable relationship.
     * Get the postable model (ImagePost, VideoPost, or AudioPost).
     *
     * @return MorphTo<Model, $this>
     */
    public function postable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the post type enum value.
     * Returns null if postable_type is not set.
     */
    public function getPostTypeAttribute(): ?PostTypeEnum
    {
        if (blank($this->postable_type)) {
            return null;
        }

        return match ($this->postable_type) {
            ImagePost::class => PostTypeEnum::IMAGE,
            VideoPost::class => PostTypeEnum::VIDEO,
            AudioPost::class => PostTypeEnum::AUDIO,
            NewsletterPost::class => PostTypeEnum::NEWSLETTER,
            default => null,
        };
    }

    /**
     * Check if this post is of a specific type.
     */
    public function isType(PostTypeEnum $type): bool
    {
        return $this->postable_type === $type->model();
    }

    /**
     * Get the author that owns the post.
     *
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get all taxonomy terms attached to the post.
     *
     * @return MorphToMany<TaxonomyTerm, $this>
     */
    public function taxonomyTerms(): MorphToMany
    {
        return $this->morphToMany(TaxonomyTerm::class, 'taggable', 'taggables');
    }

    /**
     * Get all comments on the post.
     *
     * @return MorphMany<Comment, $this>
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Scope a query to only include published posts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope a query to only include draft posts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to exclude newsletter posts.
     */
    public function scopeExcludeNewsletters($query)
    {
        return $query->where('postable_type', '!=', NewsletterPost::class);
    }

    /**
     * Register media conversions for the post.
     *
     * Defines image conversions for different use cases:
     * - featured: 1200x630 for social sharing (Open Graph)
     * - thumbnail: 368x232 for listing views
     * - gallery: 800x600 for gallery displays
     * - preview: 300x300 for admin previews
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        // Featured image conversion - optimized for social sharing (Open Graph)
        $this->addMediaConversion('featured')
            ->fit(Fit::Crop, 1200, 630)
            ->quality(85)
            ->format('webp')
            ->performOnCollections('featured', 'images');

        // Thumbnail conversion - for listing views
        $this->addMediaConversion('thumbnail')
            ->fit(Fit::Crop, 368, 232)
            ->quality(80)
            ->format('webp')
            ->performOnCollections('featured', 'images', 'gallery');

        // Gallery conversion - for gallery displays
        $this->addMediaConversion('gallery')
            ->fit(Fit::Contain, 800, 600)
            ->quality(85)
            ->format('webp')
            ->performOnCollections('gallery', 'images');

        // Preview conversion - for admin previews
        $this->addMediaConversion('preview')
            ->fit(Fit::Crop, 300, 300)
            ->quality(75)
            ->format('webp')
            ->nonQueued()
            ->performOnCollections('featured', 'images', 'gallery');
    }

    /**
     * Register media collections for the post.
     *
     * Defines collections:
     * - featured: Single featured image per post
     * - gallery: Multiple gallery images
     * - images: General images collection
     */
    public function registerMediaCollections(): void
    {
        // Single featured image per post
        $this->addMediaCollection('featured')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif']);

        // Multiple gallery images
        $this->addMediaCollection('gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif']);

        // General images collection
        $this->addMediaCollection('images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif']);
    }

    /**
     * Get the featured image URL.
     *
     * @param  string  $conversion  The image conversion to use (featured, thumbnail, gallery, preview)
     * @return string|null The URL of the featured image, or null if not set
     */
    public function getFeaturedImageUrl(string $conversion = 'featured'): ?string
    {
        $media = $this->getFirstMedia('featured');

        return $media?->getUrl($conversion);
    }

    /**
     * Get all gallery image URLs.
     *
     * @param  string  $conversion  The image conversion to use
     * @return array<int, string> Array of gallery image URLs
     */
    public function getGalleryUrls(string $conversion = 'gallery'): array
    {
        return $this->getMedia('gallery')
            ->map(fn (Media $media) => $media->getUrl($conversion))
            ->toArray();
    }

    /**
     * Determine if the model should be searchable.
     *
     * Only published posts should be indexed for search.
     */
    public function shouldBeSearchable(): bool
    {
        return $this->status === 'published'
            && $this->published_at !== null
            && $this->published_at <= now();
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'slug' => $this->slug,
            'status' => $this->status,
            'published_at' => $this->published_at?->toIso8601String(),
        ];
    }

    /**
     * Get the content rendered as HTML from Markdown.
     * Cached for performance.
     */
    /**
     * Get dynamic SEO data from post fields.
     */
    /**
     * Get dynamic SEO data from post fields.
     *
     * Note: Callers should eager load relations for performance:
     * Post::with(['seo', 'author', 'taxonomyTerms', 'media'])->...
     */
    public function getDynamicSEOData(): \RalphJSmit\Laravel\SEO\Support\SEOData
    {
        // Defensively load missing relations to prevent N+1
        $this->loadMissing(['seo', 'author', 'taxonomyTerms', 'media']);

        return new \RalphJSmit\Laravel\SEO\Support\SEOData(
            title: $this->seo?->title ?? $this->title,
            description: $this->seo?->description ?? $this->excerpt,
            image: $this->seo?->image ?? $this->getFirstMediaUrl('featured'),
            author: $this->author?->name ?? 'Admin',
            published_time: $this->published_at ?? $this->created_at,
            modified_time: $this->updated_at,
            section: $this->taxonomyTerms->first()?->name,
        );
    }

    public function getContentHtmlAttribute(): ?string
    {
        $content = $this->content;
        
        if (empty($content)) {
            return null;
        }

        $cacheKey = "post:{$this->id}:content_html";

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($content) {
            $converter = new \League\CommonMark\GithubFlavoredMarkdownConverter([
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ]);

            return $converter->convert($content)->getContent();
        });
    }

    /**
     * Get the excerpt rendered as HTML from Markdown.
     * Cached for performance.
     */
    public function getExcerptHtmlAttribute(): ?string
    {
        $excerpt = $this->excerpt;
        
        if (empty($excerpt)) {
            return null;
        }

        $cacheKey = "post:{$this->id}:excerpt_html";

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($excerpt) {
            $converter = new \League\CommonMark\GithubFlavoredMarkdownConverter([
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ]);

            return $converter->convert($excerpt)->getContent();
        });
    }

    /**
     * Clear the HTML content cache when the post is saved or deleted.
     */
    protected static function booted(): void
    {
        static::saved(function (Post $post): void {
            Cache::forget("post:{$post->id}:content_html");
            Cache::forget("post:{$post->id}:excerpt_html");
        });

        static::deleted(function (Post $post): void {
            Cache::forget("post:{$post->id}:content_html");
            Cache::forget("post:{$post->id}:excerpt_html");
        });
    }
}
