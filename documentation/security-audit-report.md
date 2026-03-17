# CloudHerder CMS Security Audit Report

**Audit Date:** 2026-03-17  
**Auditor:** The Foundry Security Agent  
**Framework:** Laravel 12.x with Livewire  
**Risk Assessment:** HIGH (Multiple critical issues identified)

---

## Executive Summary

The CloudHerder CMS codebase has been analyzed against OWASP Top 10 security risks and Laravel-specific security best practices. **Several critical security vulnerabilities have been identified that must be addressed before production deployment.**

### Overall Risk Level: **HIGH**

| Category | Count |
|----------|-------|
| Critical Issues | 4 |
| High Issues | 3 |
| Medium Issues | 5 |
| Low Issues | 7 |

### Immediate Action Required

1. **Stored XSS vulnerabilities** in page/newsletter content rendering
2. **Missing rate limiting** on critical endpoints
3. **Known vulnerable dependency** (CommonMark CVE-2026-30838)
4. **Insufficient input validation** in API endpoints

---

## Critical Findings (Blockers for Production)

### 1. Stored Cross-Site Scripting (XSS) - Page Content [CRITICAL]

**OWASP Category:** A03:2021 – Injection  
**Affected Files:**
- `resources/views/pages/show.blade.php` (line: `{!! $page->content !!}`)
- `resources/views/newsletters/show.blade.php` (line: `{!! $post->content !!}`)
- `resources/views/admin/pages/show.blade.php` (line: `{!! $page->content !!}`)

**Description:**  
The application uses unescaped Blade syntax (`{!! !!}`) to render page and newsletter content. This allows any HTML/Script injected by content authors or through compromised accounts to execute in visitors' browsers.

**Attack Scenario:**
1. An attacker with content editing privileges (or via compromised account) creates a page with malicious JavaScript
2. The script executes in the browser of every visitor to that page
3. This could steal session cookies, perform actions on behalf of users, or deface the site

**Remediation:**
```php
// Option 1: Use Laravel's Purifier package
composer require mews/purifier

// In view:
{!! clean($page->content) !!}

// Option 2: Use escaped output with HTML whitelist
{{ $page->content }} // For plain text only

// Option 3: Implement Content Security Policy headers
// Add to middleware:
$response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self'");
```

**Priority:** CRITICAL - Fix before production deployment

---

### 2. RSS Feed XSS Vulnerability [CRITICAL]

**OWASP Category:** A03:2021 – Injection  
**Affected File:** `resources/views/rss/feed.blade.php`

**Description:**  
The RSS feed uses CDATA sections with raw content output that doesn't escape special XML characters or sanitize HTML content:
```php
<description><![CDATA[{{ $post->excerpt ?? $post->content }}]]></description>
```

While Blade's `{{ }}` escapes HTML entities, the combination with CDATA and raw post content could lead to XML injection or XSS when the feed is consumed by RSS readers.

**Remediation:**
```php
<description>{{ strip_tags($post->excerpt ?? $post->content) }}</description>
```

**Priority:** CRITICAL

---

### 3. Known Vulnerable Dependency - CommonMark [CRITICAL]

**OWASP Category:** A06:2021 – Vulnerable and Outdated Components  
**CVE:** CVE-2026-30838  
**Package:** league/commonmark <= 2.8.0

**Description:**  
The application uses CommonMark for Markdown processing which has a known security vulnerability allowing HTML tag bypass via whitespace in tag names.

**Affected Code:**
- `app/Models/Post.php` - `getContentHtmlAttribute()` and `getExcerptHtmlAttribute()`

**Remediation:**
```bash
composer update league/commonmark
```

Verify the update:
```bash
composer audit
```

**Priority:** CRITICAL - Update immediately

---

### 4. Inadequate Rate Limiting on API Endpoints [CRITICAL]

**OWASP Category:** A07:2021 – Identification and Authentication Failures  
**Affected Endpoints:**
- `POST /api/v1/newsletter/subscribe`
- `POST /api/v1/newsletter/unsubscribe`
- `GET /api/v1/newsletter/status`

**Description:**  
The newsletter API endpoints have no rate limiting, making them susceptible to:
- Email enumeration attacks
- Spam subscription attacks
- DoS via resource exhaustion

**Remediation:**
Add to `app/Providers/AppServiceProvider.php`:
```php
RateLimiter::for('newsletter', function (Request $request) {
    return Limit::perMinute(5)->by($request->ip());
});
```

Then apply to routes in `routes/api.php`:
```php
Route::post('/newsletter/subscribe', [...])->middleware('throttle:newsletter');
```

**Priority:** CRITICAL

---

## High Priority Findings

### 5. Missing API Authentication Rate Limiting [HIGH]

**OWASP Category:** A07:2021 – Identification and Authentication Failures  
**Affected Endpoints:**
- All `/api/v1/*` endpoints protected by `auth:sanctum`

**Description:**  
While web routes have rate limiting configured, API routes lack specific rate limiting for authenticated users, allowing potential abuse of API tokens.

**Remediation:**
```php
// In RouteServiceProvider or AppServiceProvider
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});
```

**Priority:** HIGH

---

### 6. Unrestricted File Upload in MediaUploader [HIGH]

**OWASP Category:** A01:2021 – Broken Access Control / A03:2021 – Injection  
**Affected File:** `app/Livewire/MediaUploader.php`

**Description:**  
The MediaUploader component has several security issues:

1. **MIME type validation is insufficient** - Only checks file extension via `max:10240`
2. **No file type whitelist** - Theoretically accepts any file type
3. **Files stored directly without virus scanning**
4. **Path traversal risk** - Uses user-provided collection names

**Current vulnerable code:**
```php
'files.*' => ['file', 'max:10240'], // Only size limit
```

**Remediation:**
```php
$this->validate([
    'files' => ['required', 'array', 'max:20'],
    'files.*' => [
        'file',
        'max:10240',
        'mimes:jpg,jpeg,png,webp,gif,pdf,doc,docx',
        function ($attribute, $value, $fail) {
            // Validate actual MIME type matches extension
            $mime = $value->getMimeType();
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
            if (!in_array($mime, $allowedMimes)) {
                $fail('Invalid file type.');
            }
        },
    ],
]);

// Store outside web root or with .htaccess protection
$path = $file->store('media/' . auth()->id(), 'private');
```

**Priority:** HIGH

---

### 7. Missing HTTPS Enforcement Configuration [HIGH]

**OWASP Category:** A02:2021 – Cryptographic Failures  
**Affected Areas:** Session cookies, authentication tokens

**Description:**  
The session configuration (`config/session.php`) uses environment-based secure cookie settings that default to null/insecure:
```php
'secure' => env('SESSION_SECURE_COOKIE'), // Defaults to null
```

If not explicitly set to `true` in production, session cookies will be transmitted over HTTP.

**Remediation:**
Ensure `.env.production` contains:
```env
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict
SANCTUM_STATEFUL_DOMAINS=cloudherder.nz
```

Add HSTS middleware:
```php
// In AppServiceProvider or middleware
if (app()->environment('production')) {
    URL::forceScheme('https');
}
```

**Priority:** HIGH

---

## Medium Priority Findings

### 8. Insufficient Logging for Security Events [MEDIUM]

**OWASP Category:** A09:2021 – Security Logging and Monitoring Failures

**Description:**  
No centralized logging for:
- Failed authentication attempts
- Permission violations
- Data modification events
- API access anomalies

**Remediation:**
Create a security logging middleware:
```php
// Log failed auth attempts
Log::channel('security')->warning('Authentication failed', [
    'email' => $request->input('email'),
    'ip' => $request->ip(),
    'user_agent' => $request->userAgent(),
    'timestamp' => now(),
]);
```

Configure dedicated security log channel in `config/logging.php`.

**Priority:** MEDIUM

---

### 9. Missing Input Sanitization in Comment API [MEDIUM]

**OWASP Category:** A03:2021 – Injection  
**Affected File:** `app/Http/Controllers/Api/CommentController.php`

**Description:**  
Comment bodies are stored and rendered without HTML sanitization:
```php
$comment = new Comment([
    'body' => $validated['body'], // No sanitization
]);
```

**Remediation:**
```php
use Illuminate\Support\Str;

$comment = new Comment([
    'body' => strip_tags($validated['body']), // Remove all HTML
    // OR use HTML Purifier for limited HTML support
]);
```

**Priority:** MEDIUM

---

### 10. Missing Email Verification on Newsletter Subscribe [MEDIUM]

**OWASP Category:** A07:2021 – Identification and Authentication Failures

**Description:**  
The newsletter subscription confirmation token is 64 random characters but:
- No expiration time on tokens
- Tokens are single-use but remain in database indefinitely
- No rate limiting on confirmation attempts

**Remediation:**
```php
// Add to NewsletterSubscriber model
protected static function booted(): void
{
    static::creating(function (self $subscriber): void {
        if (empty($subscriber->confirmation_token)) {
            $subscriber->confirmation_token = Str::random(64);
            $subscriber->token_expires_at = now()->addHours(24); // Add expiration
        }
        // ...
    });
}

// In controller, check expiration
public function confirm(string $token): JsonResponse
{
    $subscriber = NewsletterSubscriber::where('confirmation_token', $token)
        ->where('token_expires_at', '>', now()) // Check expiration
        ->first();
    
    if (!$subscriber) {
        return response()->json(['message' => 'Invalid or expired token.'], 404);
    }
    // ...
}
```

**Priority:** MEDIUM

---

### 11. Information Disclosure via Error Messages [MEDIUM]

**OWASP Category:** A05:2021 – Security Misconfiguration

**Description:**  
While `APP_DEBUG=false` is set in production `.env`, API error responses may still leak information about the database structure or file paths.

**Remediation:**
Create custom exception handler for API routes:
```php
// In bootstrap/app.php ->withExceptions
$exceptions->renderable(function (Throwable $e, Request $request) {
    if ($request->is('api/*')) {
        return response()->json([
            'message' => 'An error occurred.',
            'reference_id' => app('sentry')->getLastEventId(), // If using Sentry
        ], 500);
    }
});
```

**Priority:** MEDIUM

---

### 12. Weak Password Policy in Development Mode [MEDIUM]

**OWASP Category:** A07:2021 – Identification and Authentication Failures

**Description:**  
The password validation in `AppServiceProvider` only enforces strong passwords in production:
```php
Password::defaults(fn (): ?Password => app()->isProduction()
    ? Password::min(12)->mixedCase()->letters()->numbers()->symbols()->uncompromised()
    : null // No requirements in non-production!
);
```

**Remediation:**
Always enforce minimum password standards:
```php
Password::defaults(fn (): Password => app()->isProduction()
    ? Password::min(12)->mixedCase()->letters()->numbers()->symbols()->uncompromised()
    : Password::min(8)->mixedCase()->letters()->numbers() // Minimum for dev too
);
```

**Priority:** MEDIUM

---

## Low Priority Findings (Recommendations)

### 13. Missing Content Security Policy Headers [LOW]

**OWASP Category:** A05:2021 – Security Misconfiguration

**Recommendation:**
Add CSP headers via middleware:
```php
$response->headers->set('Content-Security-Policy', 
    "default-src 'self'; " .
    "script-src 'self' 'unsafe-inline'; " .
    "style-src 'self' 'unsafe-inline'; " .
    "img-src 'self' data: https:;"
);
```

---

### 14. Missing X-Frame-Options Header [LOW]

**OWASP Category:** A05:2021 – Security Misconfiguration

**Recommendation:**
```php
$response->headers->set('X-Frame-Options', 'DENY');
// OR for specific framing
$response->headers->set('X-Frame-Options', 'SAMEORIGIN');
```

---

### 15. Missing X-Content-Type-Options Header [LOW]

**OWASP Category:** A05:2021 – Security Misconfiguration

**Recommendation:**
```php
$response->headers->set('X-Content-Type-Options', 'nosniff');
```

---

### 16. Database Credentials in .env.example [LOW]

**OWASP Category:** A05:2021 – Security Misconfiguration

**Description:**  
The `.env.example` file should not contain any default credentials that might accidentally be copied to production.

**Recommendation:**
Remove all default values for sensitive fields in `.env.example`:
```env
DB_PASSWORD=
MAIL_PASSWORD=
REDIS_PASSWORD=
```

---

### 17. Missing Database Transaction for Complex Operations [LOW]

**OWASP Category:** A04:2021 – Insecure Design

**Description:**  
In `PostApiController::store()`, post and postable entity creation are not wrapped in a database transaction.

**Recommendation:**
```php
use Illuminate\Support\Facades\DB;

public function store(StorePostRequest $request): PostResource|JsonResponse
{
    return DB::transaction(function () use ($request) {
        $postable = $this->createPostable(...);
        $post = Post::create([...]);
        // ...
        return new PostResource($post);
    });
}
```

---

### 18. Session Fixation Protection [LOW]

**OWASP Category:** A07:2021 – Identification and Authentication Failures

**Recommendation:**
Laravel handles this by default, but verify `SESSION_DRIVER=database` and regenerate session on login:
```php
// Already handled by Fortify, but verify it's not disabled
```

---

### 19. Missing Subresource Integrity (SRI) [LOW]

**OWASP Category:** A06:2021 – Vulnerable and Outdated Components

**Recommendation:**
If loading external JS/CSS, add integrity attributes:
```html
<script src="https://cdn.example.com/lib.js" 
        integrity="sha384-..." 
        crossorigin="anonymous"></script>
```

---

## Positive Security Controls Identified

The following security best practices are correctly implemented:

1. ✅ **CSRF Protection:** All forms use `@csrf` directive
2. ✅ **SQL Injection Prevention:** Uses Eloquent ORM with parameterized queries throughout
3. ✅ **Password Hashing:** Uses Laravel's bcrypt with proper cost factor (BCRYPT_ROUNDS=12)
4. ✅ **Authorization:** Spatie Laravel Permission properly implemented
5. ✅ **Two-Factor Authentication:** Laravel Fortify 2FA enabled with confirmation
6. ✅ **Session Security:** Database-backed sessions with proper lifetime
7. ✅ **Route Protection:** Middleware-based permission checks on admin routes
8. ✅ **Mass Assignment Protection:** All models use `$fillable` (not `$guarded`)
9. ✅ **Email Verification:** Laravel Fortify email verification enabled
10. ✅ **Rate Limiting:** Search and contact forms have rate limiting
11. ✅ **File Storage:** Uses Laravel's filesystem abstraction (not direct file operations)
12. ✅ **Soft Deletes:** Properly implemented on critical models
13. ✅ **API Authentication:** Sanctum properly configured with token abilities
14. ✅ **Honeypot/Blocklist:** Contact form has blocklist functionality
15. ✅ **Secure Headers:** Session cookies use http_only by default

---

## Remediation Priority Matrix

| Issue | Priority | Effort | Impact | OWASP |
|-------|----------|--------|--------|-------|
| XSS in Page Content | CRITICAL | Low | High | A03 |
| XSS in RSS Feed | CRITICAL | Low | Medium | A03 |
| CommonMark CVE | CRITICAL | Low | High | A06 |
| API Rate Limiting | CRITICAL | Medium | High | A07 |
| HTTPS Enforcement | HIGH | Low | High | A02 |
| File Upload Security | HIGH | Medium | High | A01/A03 |
| API Auth Rate Limiting | HIGH | Medium | Medium | A07 |
| Security Logging | MEDIUM | High | Medium | A09 |
| Comment Sanitization | MEDIUM | Low | Medium | A03 |
| Token Expiration | MEDIUM | Low | Low | A07 |
| Error Handling | MEDIUM | Medium | Low | A05 |
| Password Policy | MEDIUM | Low | Low | A07 |
| CSP Headers | LOW | Low | Medium | A05 |
| Security Headers | LOW | Low | Low | A05 |
| Database Transactions | LOW | Low | Low | A04 |

---

## Recommended Security Tools

1. **Dependency Scanning:** `composer audit` (run regularly)
2. **Static Analysis:** `phpstan` with security rules
3. **Vulnerability Database:** Subscribe to Laravel Security Advisories
4. **Security Headers:** Use `bepsvpt/secure-headers` package
5. **WAF:** Consider Cloudflare or AWS WAF for production
6. **Monitoring:** Implement Sentry or similar for error tracking

---

## Compliance Notes

- **GDPR:** Consider implementing data retention policies for newsletter subscribers and contact form submissions
- **PCI DSS:** Not applicable (no payment processing observed)
- **SOC 2:** Security logging and monitoring improvements recommended

---

*This audit represents a point-in-time assessment. Security is an ongoing process requiring regular reviews, updates, and monitoring.*
