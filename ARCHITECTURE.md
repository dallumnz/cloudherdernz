# CloudHerder NZ

**Date:** 2026-03-01
**Feature:** Markdown Support

## Implementation Plan

### 1. Requirements Summary
- Enable markdown parsing and rendering across the application.
- Provide a reusable service for converting markdown to HTML.
- Add a `markdown` column to relevant models (e.g., Post, Comment).
- Update controllers to accept markdown input and store it.
- Render stored markdown in views using the service.
- Write unit tests for the service and feature tests for CRUD operations.

### 2. Database Schema Changes
```php
// database/migrations/2026_03_01_000000_add_markdown_to_posts.php
Schema::table('posts', function (Blueprint $table) {
    $table->text('markdown')->nullable();
});
```

### 3. Models Required / Updated
| Model | Location | Purpose |
|-------|----------|---------|
| Post | app/Models/Post.php | Add `markdown` attribute, cast to array if needed |
| Comment | app/Models/Comment.php | Add `markdown` attribute |

### 4. Services
- Create `app/Services/MarkdownService.php` using league/commonmark.

### 5. Controllers
- Update `PostController` and `CommentController` to handle markdown input via Form Requests.

### 6. Routes
```php
Route::resource('posts', PostController::class);
Route::resource('comments', CommentController::class);
```

### 7. Views
- Use Blade components or Livewire slots to render `{{ $post->markdown | markdown }}`.
- Add a TinyMCE/Markdown editor in forms.

### 8. Tests
| Test | Location | Purpose |
|------|----------|---------|
| MarkdownServiceTest.php | tests/Unit/Services/ | Ensure conversion works |
| PostFeatureTest.php | tests/Feature/ | CRUD with markdown |

### 9. Edge Cases & Validation
- Validate `markdown` is a string and not too large.
- Escape HTML in output to prevent XSS.

### 10. Integration Points
- Use existing `AppServiceProvider` to bind the Markdown service.
- Add middleware if needed for sanitization.

---

**Next Steps**
1. Generate migration and run `php artisan migrate`.
2. Implement `MarkdownService`.
3. Update models, controllers, routes, views.
4. Write tests and run `php artisan test --compact`.
5. Commit changes.
