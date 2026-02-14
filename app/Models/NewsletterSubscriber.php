<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Newsletter Subscriber Model
 *
 * Represents a newsletter subscriber in the system.
 *
 * @property int $id
 * @property string $email
 * @property string|null $name
 * @property string $status
 * @property \Carbon\Carbon|null $subscribed_at
 * @property \Carbon\Carbon|null $unsubscribed_at
 * @property string|null $ip_address
 * @property string|null $confirmation_token
 * @property \Carbon\Carbon|null $confirmed_at
 * @property array|null $preferences
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class NewsletterSubscriber extends Model
{
    /** @use HasFactory<\Database\Factories\NewsletterSubscriberFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'name',
        'status',
        'subscribed_at',
        'unsubscribed_at',
        'ip_address',
        'confirmation_token',
        'confirmed_at',
        'preferences',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subscribed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'preferences' => 'array',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::creating(function (self $subscriber): void {
            if (empty($subscriber->confirmation_token)) {
                $subscriber->confirmation_token = Str::random(64);
            }
            if (empty($subscriber->subscribed_at)) {
                $subscriber->subscribed_at = now();
            }
        });
    }

    /**
     * Scope a query to only include active subscribers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include pending subscribers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include unsubscribed subscribers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnsubscribed($query)
    {
        return $query->where('status', 'unsubscribed');
    }

    /**
     * Scope a query to only include confirmed subscribers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeConfirmed($query)
    {
        return $query->whereNotNull('confirmed_at');
    }

    /**
     * Scope a query to only include unconfirmed subscribers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnconfirmed($query)
    {
        return $query->whereNull('confirmed_at');
    }

    /**
     * Check if the subscriber is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the subscriber is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the subscriber is unsubscribed.
     */
    public function isUnsubscribed(): bool
    {
        return $this->status === 'unsubscribed';
    }

    /**
     * Check if the subscriber is confirmed.
     */
    public function isConfirmed(): bool
    {
        return $this->confirmed_at !== null;
    }

    /**
     * Confirm the subscriber.
     */
    public function confirm(): void
    {
        $this->update([
            'confirmed_at' => now(),
            'status' => 'active',
            'confirmation_token' => null,
        ]);
    }

    /**
     * Unsubscribe the subscriber.
     */
    public function unsubscribe(): void
    {
        $this->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
        ]);
    }

    /**
     * Reactivate the subscriber.
     */
    public function reactivate(): void
    {
        $this->update([
            'status' => 'active',
            'unsubscribed_at' => null,
        ]);
    }

    /**
     * Get the subscriber's display name.
     */
    public function displayName(): string
    {
        return $this->name ?? $this->email;
    }

    /**
     * Find a subscriber by their confirmation token.
     */
    public static function findByToken(string $token): ?self
    {
        return self::where('confirmation_token', $token)->first();
    }
}
