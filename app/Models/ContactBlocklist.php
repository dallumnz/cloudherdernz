<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * ContactBlocklist Model
 *
 * Represents blocked emails or domains for contact form submissions.
 *
 * @property int $id
 * @property string $type
 * @property string $value
 * @property string|null $reason
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class ContactBlocklist extends Model
{
    /** @use HasFactory<\Database\Factories\ContactBlocklistFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contact_blocklist';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'value',
        'reason',
    ];

    /**
     * Check if an email is blocked.
     */
    public static function isEmailBlocked(string $email): bool
    {
        // Check exact email match
        if (self::where('type', 'email')->where('value', $email)->exists()) {
            return true;
        }

        // Check domain match
        $domain = str($email)->after('@')->toString();

        return self::where('type', 'domain')->where('value', $domain)->exists();
    }

    /**
     * Block an email address.
     */
    public static function blockEmail(string $email, ?string $reason = null): self
    {
        return self::firstOrCreate(
            ['type' => 'email', 'value' => $email],
            ['reason' => $reason]
        );
    }

    /**
     * Block a domain.
     */
    public static function blockDomain(string $domain, ?string $reason = null): self
    {
        return self::firstOrCreate(
            ['type' => 'domain', 'value' => $domain],
            ['reason' => $reason]
        );
    }

    /**
     * Unblock an email or domain.
     */
    public static function unblock(string $type, string $value): bool
    {
        return self::where('type', $type)->where('value', $value)->forceDelete() > 0;
    }

    /**
     * Scope a query to only include email blocks.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEmails($query)
    {
        return $query->where('type', 'email');
    }

    /**
     * Scope a query to only include domain blocks.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDomains($query)
    {
        return $query->where('type', 'domain');
    }
}
