<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Page Model
 *
 * Represents a static page in the system. Pages can be published or draft
 * and are accessible via their unique slug.
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $content
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string $status
 * @property \Carbon\Carbon|null $published_at
 * @property int $author_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read User $author
 */
class Page extends Model
{
    /** @use HasFactory<\Database\Factories\PageFactory> */
    use HasFactory;

    use SoftDeletes;
    use \RalphJSmit\Laravel\SEO\Support\HasSEO;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'status',
        'published_at',
        'author_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    /**
     * Get the author that owns the page.
     *
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Scope a query to only include published pages.
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
     * Scope a query to only include draft pages.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to find a page by its slug.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }

    /**
     * Check if the page is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published'
            && $this->published_at !== null
            && $this->published_at <= now();
    }

    /**
     * Check if the page is a draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Publish the page.
     */
    public function publish(): void
    {
        $this->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    /**
     * Unpublish the page (set to draft).
     */
    public function unpublish(): void
    {
        $this->update([
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    /**
     * Get the SEO-friendly meta title.
     * Falls back to page title if meta_title is not set.
     */
    public function getSeoTitle(): string
    {
        return $this->meta_title ?? $this->title;
    }

    /**
     * Get the SEO-friendly meta description.
     * Falls back to excerpt from content if meta_description is not set.
     */
    public function getSeoDescription(): ?string
    {
        if ($this->meta_description) {
            return $this->meta_description;
        }

        if ($this->content) {
            return str(strip_tags($this->content))->limit(160);
        }

        return null;
    }

    /**
     * Get dynamic SEO data from page fields.
     *
     * Note: Callers should eager load relations for performance:
     * Page::with(['author'])->...
     */
    public function getDynamicSEOData(): \RalphJSmit\Laravel\SEO\Support\SEOData
    {
        // Defensively load missing relations to prevent N+1
        $this->loadMissing(['author']);

        return new \RalphJSmit\Laravel\SEO\Support\SEOData(
            title: $this->meta_title ?? $this->title,
            description: $this->meta_description ?? ($this->content ? str(strip_tags($this->content))->limit(160) : null),
            author: $this->author?->name ?? 'Admin',
            published_time: $this->published_at ?? $this->created_at,
            modified_time: $this->updated_at,
        );
    }
}
