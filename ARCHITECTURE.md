# CloudHerderNZ Comment System Architecture

**Date:** 2026-02-20
**Feature:** Comment System

## Overview
The comment system is a polymorphic, nested‑comment feature that supports CRUD operations, moderation, and real‑time notifications. It integrates with the existing Post model and any other entities that may need comments.

## Database Schema
| Table | Columns | Notes |
|-------|---------|-------|
| `comments` | id, commentable_type, commentable_id, user_id, parent_id, body, is_approved, deleted_at, created_at, updated_at | Polymorphic relation (`commentable_*`). `parent_id` for nested comments. Soft deletes enable moderation.
| `notifications` (Laravel default) | ... | Used to notify post authors of new comments.

### Migration: create_comments_table.php
```php
Schema::create('comments', function (Blueprint $table) {
    $table->id();
    $table->morphs('commentable'); // commentable_type, commentable_id
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('parent_id')->nullable()->constrained('comments')->nullOnDelete();
    $table->text('body');
    $table->boolean('is_approved')->default(false);
    $table->softDeletes();
    $table->timestamps();
});
```

## Models
- **Comment** (`app/Models/Comment.php`)
  - `morphTo('commentable')`
  - `belongsTo(User::class)`
  - `parent()` and `children()` relationships for nesting.
  - Scope `approved()`.

- **Post** (existing) adds `comments()` morphMany.

## Controllers
`app/Http/Controllers/API/CommentController.php`
```php
public function index(Request $request, string $commentableType, int $commentableId)
public function store(StoreCommentRequest $request, string $commentableType, int $commentableId)
public function show(Comment $comment)
public function update(UpdateCommentRequest $request, Comment $comment)
public function destroy(Comment $comment)
```

## Routes (api.php)
```php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('{type}/{id}/comments', [CommentController::class, 'index']);
    Route::post('{type}/{id}/comments', [CommentController::class, 'store']);
    Route::put('comments/{comment}', [CommentController::class, 'update']);
    Route::delete('comments/{comment}', [CommentController::class, 'destroy']);
});
```

## Policies
`app/Policies/CommentPolicy.php`
- `viewAny`, `create`, `update`, `delete`. Users can modify own comments; admins can modify any.

## Events & Listeners
- **Event**: `App\Events\CommentCreated` (payload: comment). Triggered in `store()` after approval.
- **Listener**: `SendNewCommentNotification` – sends a notification to the post author if not the commenter.

## Notifications
`app/Notifications/NewComment.php`
- Uses `toMail` and `toDatabase`. Contains link to the comment.

## Tests Plan
| Test | Purpose |
|------|---------|
| CommentFactory | Generates comments for tests |
| CommentPolicyTest | Verify permissions for users/admins |
| CommentApiTest | CRUD endpoints, validation, auth, moderation |
| NotificationTest | Ensure notification sent on new comment |

## Next Steps
1. Create migrations and run `php artisan migrate`.
2. Generate models with factories.
3. Implement controller logic and policy checks.
4. Wire up event/listener and notification.
5. Write tests following the plan.
