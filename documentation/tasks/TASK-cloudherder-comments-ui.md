# CloudHerder Comment System - UI Components

**Created:** 2026-02-20  
**Status:** Todo  
**Priority:** Medium  
**Related:** PR #4 (merged)

## Overview
Add frontend UI and admin dashboard for the comment system (API/backend already implemented in PR #4).

## Requirements

### Post/Page Edit Form
- [ ] Toggle to enable/disable comments on posts and pages
- [ ] Add `comments_enabled` column to posts/pages tables (if not added)

### Public UI
- [ ] `CommentForm` - comment input form (authenticated users)
- [ ] `CommentList` - display threaded comments on posts/pages
- [ ] Show comments section only when `comments_enabled` is true

### Admin Dashboard (Livewire)
- [ ] Comment Manager UI - list all comments with filters (pending, approved, rejected)
- [ ] Moderation controls: approve, reject, delete
- [ ] Link to comment in context (view post/page)

### Roles & Permissions
- [ ] UI for admin to assign "moderate comments" permission to users
- [ ] May require using existing roles

## Technical Details

### Database
- Check if `comments_enabled` exists on posts/pages, add if missing

### Livewire Components
- `app/Livewire/CommentForm.php` - submit new comment
- `app/Livewire/CommentList.php` - display comments with replies
- `app/Livewire/Admin/CommentManager.php` - moderation dashboard

### Views
- Update post show templates to include CommentList and CommentForm
- Create admin comments management page

## Out of Scope (v1)
- Real-time updates (WebSocket)
- Email notifications
- Comment likes/reactions

## Notes
- Use existing Livewire component patterns from the project
- Follow existing admin dashboard styling
