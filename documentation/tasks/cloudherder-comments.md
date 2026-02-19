# CloudHerder Comment System

**Created:** 2026-02-19  
**Status:** Todo  
**Priority:** Medium

## Overview
Add a comment system to CloudHerder allowing authenticated users to comment on posts and pages, with manual moderation by Editor+ roles.

## Requirements

### Core Features
- [ ] Comment model (polymorphic - works for posts, pages, and any commentable content)
- [ ] Comments table migration
- [ ] Auth required (no guest comments)
- [ ] Manual moderation (approve/reject) - Editor role or higher

### UI Components
- [ ] Toggle to enable/disable comments on post/page edit form
- [ ] Comment form on posts/pages (Livewire component)
- [ ] Comment display (threaded or flat)
- [ ] Comment list on post/page
- [ ] Comment Manager UI in admin dashboard
- [ ] Moderation controls (approve/reject/delete)

### Permissions
- [ ] `view comments` - view all comments in admin
- [ ] `create comments` - add comments (authenticated users)
- [ ] `edit comments` - edit own comments (before moderation)
- [ ] `delete comments` - delete own comments
- [ ] `moderate comments` - approve/reject any comment (Editor+)

## Technical Details

### Database Schema (Migration)
```php
// comments table
- id
- user_id (FK to users)
- commentable_id (polymorphic)
- commentable_type (polymorphic)
- parent_id (nullable, for threaded replies)
- body (text)
- status (enum: pending, approved, rejected, spam)
- created_at
- updated_at
```

### Database Schema (Migration)
```php
// comments table
- id
- user_id (FK to users)
- commentable_id (polymorphic)
- commentable_type (polymorphic)
- parent_id (nullable, for threaded replies)
- body (text)
- status (enum: pending, approved, rejected, spam)
- created_at
- updated_at

// posts/pages table add:
// - comments_enabled (boolean, default true)
```

### Models
- `Comment` - polymorphic relationship with `commentable()`
- Update `User` model with `comments()` relationship
- Update `Post`/`Page` models with `comments()` relationship
- Add `comments_enabled` attribute to Post/Page (default: true)

### Livewire Components
- `CommentList` - display comments on post/page
- `CommentForm` - add new comment
- `CommentManager` - admin UI for moderating comments

### Permissions (seed)
- `view comments`
- `create comments`
- `edit comments`
- `delete comments`
- `moderate comments`

## Out of Scope (v1)
- Guest comments
- API endpoints
- Email notifications
- Comment reactions/likes
- Rich text formatting in comments

## Notes
- Comments display inline with posts/pages
- New comments default to `pending` status
- Moderators see pending comments in admin
- Can expand to include API later if needed
