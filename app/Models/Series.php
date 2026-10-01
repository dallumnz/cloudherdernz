<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Image\Enums\Fit;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use Illuminate\Support\Facades\Cache;

class Series extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\SeriesFactory> */
    use HasFactory;
    use HasSEO;
    use InteractsWithMedia;
    use LogsActivity;

    protected $fillable = [
        'title',
        'slug',
        'tagline',
        'description',
        'status',
        'taxonomy_term_id',
        'published_at',
    ];

    protected function casts(): array
    {
        return [];
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
     * Get the taxonomy term that links this series to its posts.
     */
    public function taxonomyTerm(): BelongsTo
    {
        return $this->belongsTo(TaxonomyTerm::class);
    }

    /**
     * Get the published posts associated with this series via its taxonomy term.
     */
    public function posts()
    {
        if (! $this->taxonomy_term_id) {
            return Post::query()->whereRaw('1 = 0');
        }

        return Post::query()
            ->published()
            ->whereHas('taxonomyTerms', fn ($q) => $q->where('taxonomy_terms.id', $this->taxonomy_term_id))
            ->orderBy('published_at', 'asc');
    }

    /**
     * Scope a query to only include published series.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope a query to only include draft series.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Check if the series is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published'
            && $this->published_at !== null
            && $this->published_at <= now();
    }

    /**
     * Check if the series is a draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Publish the series.
     */
    public function publish(): void
    {
        $this->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    /**
     * Unpublish the series (set to draft).
     */
    public function unpublish(): void
    {
        $this->update([
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get dynamic SEO data from series fields.
     */
    public function getDynamicSEOData(): \RalphJSmit\Laravel\SEO\Support\SEOData
    {
        return new \RalphJSmit\Laravel\SEO\Support\SEOData(
            title: $this->seo?->title ?? $this->title,
            description: $this->seo?->description ?? ($this->tagline ?? ($this->description ? str(strip_tags($this->description))->limit(160) : null)),
            image: $this->seo?->image ?? $this->getFirstMediaUrl('featured'),
        );
    }

    /**
     * Get the description rendered as HTML from Markdown.
     * Cached for performance.
     */
    public function getDescriptionHtmlAttribute(): ?string
    {
        $description = $this->description;

        if (empty($description)) {
            return null;
        }

        $cacheKey = "series:{$this->id}:description_html";

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($description) {
            $converter = new \League\CommonMark\GithubFlavoredMarkdownConverter([
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ]);

            return $converter->convert($description)->getContent();
        });
    }

    /**
     * Clear the HTML description cache when the series is saved or deleted.
     */
    protected static function booted(): void
    {
        static::saved(function (Series $series): void {
            Cache::forget("series:{$series->id}:description_html");
        });

        static::deleted(function (Series $series): void {
            Cache::forget("series:{$series->id}:description_html");
        });
    }

    /**
     * Register media conversions for the series.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('featured')
            ->fit(Fit::Crop, 1200, 630)
            ->quality(85)
            ->format('webp')
            ->performOnCollections('featured');

        $this->addMediaConversion('thumbnail')
            ->fit(Fit::Crop, 368, 232)
            ->quality(80)
            ->format('webp')
            ->performOnCollections('featured');
    }

    /**
     * Register media collections for the series.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif']);
    }

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'slug', 'status', 'published_at'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}
