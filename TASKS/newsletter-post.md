# NewsletterPost Implementation

**Task:** Implement NewsletterPost polymorphic type with UUID web viewing
**Project:** /home/dallum/projects/cloudherder.nz

## Overview

Create a polymorphic post type for newsletters that can be emailed to subscribers and viewed on CloudHerder via UUID links.

## Features

| Feature | Description |
|---------|-------------|
| NewsletterPost type | Polymorphic like ImagePost/VideoPost/AudioPost |
| UUID generation | Unique UUID per newsletter for web viewing |
| Email template | Title, excerpt, "View in browser" UUID link |
| Web viewing | Full render at `/newsletter/{uuid}` |
| Thunderbird-friendly | Plain text + link (images blocked) |
| Traffic driver | Link brings readers back to CloudHerder |

## Database Schema

### newsletter_posts table
```php
Schema::create('newsletter_posts', function (Blueprint $table) {
    $table->id();
    $table->string('uuid')->unique();  // For web viewing
    $table->string('subject');          // Email subject line
    $table->text('excerpt')->nullable();  // Email preview
    $table->text('content');           // Full newsletter content
    $table->timestamp('sent_at')->nullable();  // When emailed
    $table->unsignedInteger('view_count')->default(0);  // Track views
    $table->timestamps();
});
```

## Models

| Model | Location | Purpose |
|-------|----------|---------|
| NewsletterPost | app/Models/NewsletterPost.php | Newsletter post model |

## Implementation

### 1. Create NewsletterPost model
- UUID generation on create
- View count increment
- Relationship to Post (polymorphic)

### 2. Newsletter routes
```php
// Public web viewing
Route::get('/newsletter/{uuid}', [NewsletterController::class, 'show'])
    ->name('newsletter.show')
    ->where('uuid', '[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}');
```

### 3. NewsletterController
```php
// show($uuid) - Find by UUID, increment view_count, render full content
```

### 4. Email template (simple)
```blade.php
Subject: {{ $subject }}

{{ $excerpt }}

--
View in browser: {{ route('newsletter.show', $uuid) }}
```

## Integration with Posts

- NewsletterPost works like other polymorphic types
- Create NewsletterPost → Creates associated Post
- Published together
- Status tracking (draft/scheduled/sent)

## Files to Create

1. `database/migrations/xxxx_create_newsletter_posts_table.php`
2. `app/Models/NewsletterPost.php`
3. `app/Http/Controllers/NewsletterController.php`
4. Routes in `routes/web.php`

## Tests

- `NewsletterPostTest.php` - Model functionality
- `NewsletterUuidTest.php` - UUID routing and viewing

## Output

List files created and run `php artisan test`.
