<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Newsletter Post Model
 *
 * Represents a newsletter-based post type. Stores newsletter-specific data
 * like subscriber settings, template options, and delivery tracking.
 * Uses polymorphic relationship to connect with the main Post model.
 *
 * @property string $id
 * @property string|null $template
 * @property array|null $subscriber_settings
 * @property bool $is_sent
 * @property \Carbon\Carbon|null $sent_at
 * @property int|null $recipients_count
 * @property int|null $opens_count
 * @property int|null $clicks_count
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Post|null $post
 */
class NewsletterPost extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\NewsletterPostFactory> */
    use HasFactory;

    use InteractsWithMedia;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'template',
        'subscriber_settings',
        'is_sent',
        'sent_at',
        'recipients_count',
        'opens_count',
        'clicks_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subscriber_settings' => 'array',
        'is_sent' => 'boolean',
        'sent_at' => 'datetime',
        'recipients_count' => 'integer',
        'opens_count' => 'integer',
        'clicks_count' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->id)) {
                $model->id = Str::uuid()->toString();
            }
        });
    }

    /**
     * Get all posts that belong to this newsletter post.
     */
    public function posts(): MorphMany
    {
        return $this->morphMany(Post::class, 'postable');
    }

    /**
     * Register media collections for the newsletter post.
     *
     * Defines collections:
     * - attachments: Newsletter file attachments
     * - header_image: Single header image for the newsletter
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments')
            ->acceptsMimeTypes([
                'application/pdf',
                'image/jpeg',
                'image/png',
                'image/webp',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ]);

        $this->addMediaCollection('header_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    /**
     * Mark the newsletter as sent.
     */
    public function markAsSent(int $recipientsCount): void
    {
        $this->update([
            'is_sent' => true,
            'sent_at' => now(),
            'recipients_count' => $recipientsCount,
        ]);
    }

    /**
     * Record an open event.
     */
    public function recordOpen(): void
    {
        if (! $this->is_sent) {
            return;
        }

        $this->increment('opens_count');
    }

    /**
     * Record a click event.
     */
    public function recordClick(): void
    {
        if (! $this->is_sent) {
            return;
        }

        $this->increment('clicks_count');
    }

    /**
     * Get the open rate percentage.
     */
    public function getOpenRateAttribute(): ?float
    {
        if (! $this->recipients_count || $this->recipients_count === 0) {
            return null;
        }

        return round(($this->opens_count / $this->recipients_count) * 100, 2);
    }

    /**
     * Get the click rate percentage.
     */
    public function getClickRateAttribute(): ?float
    {
        if (! $this->recipients_count || $this->recipients_count === 0) {
            return null;
        }

        return round(($this->clicks_count / $this->recipients_count) * 100, 2);
    }
}
