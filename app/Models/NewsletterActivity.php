<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Newsletter Activity Model
 *
 * Tracks newsletter sending activities - queueing, sending, completion.
 *
 * @property int $id
 * @property string $newsletter_post_id
 * @property int $created_by
 * @property string $status
 * @property \Carbon\Carbon|null $scheduled_at
 * @property \Carbon\Carbon|null $sent_at
 * @property \Carbon\Carbon|null $started_at
 * @property int $recipients_count
 * @property int $sent_count
 * @property int $failed_count
 * @property string|null $mailtrap_batch_id
 * @property string|null $error_message
 * @property array|null $test_recipients
 * @property bool $is_test
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read NewsletterPost $newsletterPost
 * @property-read User $creator
 */
class NewsletterActivity extends Model
{
    /** @use HasFactory<\Database\Factories\NewsletterActivityFactory> */
    use HasFactory;

    protected $fillable = [
        'newsletter_post_id',
        'created_by',
        'status',
        'scheduled_at',
        'sent_at',
        'started_at',
        'recipients_count',
        'sent_count',
        'failed_count',
        'mailtrap_batch_id',
        'error_message',
        'test_recipients',
        'is_test',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
            'started_at' => 'datetime',
            'test_recipients' => 'array',
            'is_test' => 'boolean',
        ];
    }

    public function newsletterPost(): BelongsTo
    {
        return $this->belongsTo(NewsletterPost::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeQueued($query)
    {
        return $query->where('status', 'queued');
    }

    public function scopeSending($query)
    {
        return $query->where('status', 'sending');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isQueued(): bool
    {
        return $this->status === 'queued';
    }

    public function isSending(): bool
    {
        return $this->status === 'sending';
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['draft', 'queued']);
    }

    public function canBeRetried(): bool
    {
        return $this->status === 'failed';
    }

    public function getProgressPercentage(): int
    {
        if ($this->recipients_count === 0) {
            return 0;
        }

        return (int) round((($this->sent_count + $this->failed_count) / $this->recipients_count) * 100);
    }

    public function markAsQueued(): void
    {
        $this->update(['status' => 'queued']);
    }

    public function markAsSending(): void
    {
        $this->update([
            'status' => 'sending',
            'started_at' => now(),
        ]);
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $error,
        ]);
    }

    public function markAsCancelled(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    public function incrementSent(int $count = 1): void
    {
        $this->increment('sent_count', $count);
    }

    public function incrementFailed(int $count = 1): void
    {
        $this->increment('failed_count', $count);
    }
}
