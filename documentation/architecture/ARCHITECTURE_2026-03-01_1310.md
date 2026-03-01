# CloudHerder Architecture

**Date:** 2026-03-01
**Feature:** Markdown Editor Integration

## Overview
The Markdown editor is a reusable Livewire component powered by EasyMDE and Flux UI Free. It provides WYSIWYG editing, preview, and persistence to the database via an Eloquent model `Post`. The integration includes:
1. ✅ A Livewire component with EasyMDE integration.
2. ✅ API routes for saving drafts and publishing.
3. ✅ Form Request validation (using existing Store/Update PostRequest).
4. ✅ Tests (unit & feature) using Pest.
5. ✅ Documentation updates.

## Component Layer
- **app/Livewire/MarkdownEditor.php** – Handles state, events, and persistence.
- **resources/views/livewire/markdown-editor.blade.php** – Blade view with EasyMDE and Flux UI components.

## API Layer
- **routes/api.php** – `POST /api/posts` (create), `PUT /api/posts/{id}` (update), `PATCH /api/posts/{id}/content` (content only update).
- **app/Http/Controllers/Api/PostApiController.php** – Handles requests, uses Form Request.
- **app/Http/Requests/Post/StorePostRequest.php** – Validation rules for markdown content.
- **app/Http/Requests/Post/UpdatePostRequest.php** – Validation rules for updating posts.

## Model Layer
- **app/Models/Post.php** – Eloquent model with `content` (text) and `status` enum.
- Added accessors: `content_html`, `excerpt_html` for cached markdown rendering.
- Migration: `create_posts_table` already exists with required columns.

## Testing Strategy
- **tests/Unit/MarkdownEditorTest.php** – Tests component state changes.
- **tests/Unit/PostMarkdownTest.php** – Tests markdown HTML rendering and caching.
- **tests/Feature/Api/PostApiTest.php** – Tests API endpoints, validation, and persistence (includes content update tests).

## Edge Cases & Validation
- Empty content → handled gracefully (returns null for HTML)
- Exceeding max length (50000 chars) → 422 validation error
- Unauthorized access → 403 response

## Integration Points
- Auth via Sanctum (API guard).
- Livewire uses `wire:model.live.debounce.500ms` for live preview.
- EasyMDE via CDN for markdown editing.
- CommonMark for server-side markdown rendering.
- Cache for 24-hour HTML output caching.

## Implementation Status
✅ **COMPLETE** - All components implemented and tested.

### Files Created/Modified
| File | Status |
|------|--------|
| `app/Livewire/MarkdownEditor.php` | ✅ Created |
| `resources/views/livewire/markdown-editor.blade.php` | ✅ Created |
| `routes/api.php` | ✅ Updated (added PATCH /posts/{post}/content) |
| `app/Http/Controllers/Api/PostApiController.php` | ✅ Updated (added updateContent method) |
| `app/Models/Post.php` | ✅ Updated (added content_html, excerpt_html accessors) |
| `tests/Unit/MarkdownEditorTest.php` | ✅ Created |
| `tests/Unit/PostMarkdownTest.php` | ✅ Created |
| `tests/Feature/Api/PostApiTest.php` | ✅ Updated (added content update tests) |
| `documentation/markdown-editor.md` | ✅ Created |

## Next Steps
1. ✅ Create migration and model (Post already exists - no migration needed).
2. ✅ Implement Livewire component.
3. ✅ Add API routes & controller.
4. ✅ Write tests.
5. ✅ Update docs in `documentation/markdown-editor.md`.
