# Markdown Editor Integration

**Date:** 2026-03-01  
**Status:** Implemented  
**Component:** `MarkdownEditor` Livewire Component

## Overview

The Markdown Editor provides a rich editing experience for post content using EasyMDE (Easy Markdown Editor) integrated with Livewire and Flux UI. It supports live preview, auto-save, image uploads, and fullscreen editing.

## Features

- **EasyMDE Integration**: Full-featured markdown editor via CDN
- **Live Preview**: Real-time HTML preview with debounced updates
- **Auto-save**: Automatic draft saving every 2 seconds of inactivity
- **Image Upload**: Direct integration with media library
- **Fullscreen Mode**: Distraction-free editing environment
- **Gallery Support**: Insert image galleries into markdown
- **Cached HTML Rendering**: Markdown to HTML conversion with 24-hour caching

## Components

### Livewire Component

**File:** `app/Livewire/MarkdownEditor.php`

```php
// Basic usage in a Blade view
<livewire:markdown-editor :post-id="$post->id" />
```

#### Public Properties

| Property | Type | Description |
|----------|------|-------------|
| `postId` | `?int` | The ID of the post being edited |
| `content` | `string` | The markdown content |
| `title` | `string` | The post title (display only) |
| `previewHtml` | `string` | Rendered HTML preview |
| `isFullscreen` | `bool` | Fullscreen mode state |
| `isSaving` | `bool` | Auto-save in progress indicator |
| `lastSavedAt` | `?string` | Human-readable last save time |
| `status` | `string` | Post status (draft/published/archived) |

#### Methods

| Method | Description |
|--------|-------------|
| `autoSave()` | Saves content automatically (called on debounced changes) |
| `save()` | Manual save with validation |
| `toggleFullscreen()` | Toggle fullscreen editing mode |
| `handleImageUpload(string $url, ?string $alt)` | Insert image markdown |
| `insertGallery(array $urls)` | Insert gallery HTML block |

#### Events

| Event | Payload | Description |
|-------|---------|-------------|
| `markdown-saved` | `{ postId: int }` | Fired when content is saved |
| `toggle-fullscreen` | `{ isFullscreen: bool }` | Fired when fullscreen toggles |
| `image-inserted` | `{ markdown: string }` | Fired when image is inserted |
| `gallery-inserted` | `{ count: int }` | Fired when gallery is inserted |

### Blade View

**File:** `resources/views/livewire/markdown-editor.blade.php`

The view includes:
- EasyMDE editor with custom toolbar
- Split-pane layout (editor + preview)
- Alpine.js integration for EasyMDE lifecycle
- Flux UI components for buttons and callouts

## API Endpoints

### Update Post Content

```
PATCH /api/v1/posts/{post}/content
```

**Authentication:** Required (Sanctum)  
**Permission:** `edit posts`

**Request Body:**
```json
{
  "content": "# Markdown Content\n\nWith **formatting**"
}
```

**Response:** `200 OK` with PostResource

**Validation:**
- `content`: nullable, string, max:50000

## Post Model Enhancements

### Accessors

The `Post` model includes two new accessors for rendering markdown:

```php
// Render content as HTML (cached for 24 hours)
$post->content_html;

// Render excerpt as HTML (cached for 24 hours)
$post->excerpt_html;
```

### Cache Management

The HTML cache is automatically cleared when:
- Post is updated
- Post is deleted

Manual cache clearing:
```php
use Illuminate\Support\Facades\Cache;

Cache::forget("post:{$post->id}:content_html");
Cache::forget("post:{$post->id}:excerpt_html");
```

## Usage Examples

### In Post Manager

Update `PostManager` to use the markdown editor for content:

```blade
{{-- In post-manager.blade.php, replace the textarea --}}
@if ($editingId)
    <livewire:markdown-editor :post-id="$editingId" />
@else
    <flux:textarea wire:model="content" label="Content" />
@endif
```

### Standalone Editor Page

Create a dedicated editor route:

```php
// routes/web.php
Route::get('admin/posts/{post}/edit-content', function (Post $post) {
    return view('admin.posts.edit-content', compact('post'));
})->middleware(['auth', 'permission:edit posts'])->name('admin.posts.edit-content');
```

```blade
{{-- resources/views/admin/posts/edit-content.blade.php --}}
<x-layouts.app>
    <div class="container mx-auto py-6">
        <livewire:markdown-editor :post-id="$post->id" />
    </div>
</x-layouts.app>
```

### Displaying Rendered Content

In your post display views:

```blade
<article class="prose dark:prose-invert max-w-none">
    {!! $post->content_html !!}
</article>
```

## Image Upload Integration

The editor integrates with the existing media library:

1. Click the "Upload Image" button in the toolbar
2. Media uploader modal opens
3. Select or upload an image
4. Image is inserted as markdown: `![alt](url)`

### Customizing Image Upload

Listen for the `open-media-uploader` event in your layout:

```javascript
// In your app.js or layout
window.addEventListener('open-media-uploader', (event) => {
    // Open your media library modal
    openMediaModal(event.detail.callback, event.detail.multiple);
});
```

## Testing

### Unit Tests

**File:** `tests/Unit/MarkdownEditorTest.php`

```bash
php artisan test --filter=MarkdownEditor
```

### Feature Tests

**File:** `tests/Feature/Api/PostApiTest.php`

```bash
php artisan test --filter="Post API Content Update"
```

### Model Tests

**File:** `tests/Unit/PostMarkdownTest.php`

```bash
php artisan test --filter="Post Markdown HTML Rendering"
```

## Configuration

### EasyMDE Options

Edit the editor configuration in `markdown-editor.blade.php`:

```javascript
this.easyMDE = new EasyMDE({
    autosave: {
        enabled: true,
        uniqueId: 'post-{{ $postId ?? 'new' }}',
        delay: 1000,
    },
    spellChecker: false,
    // Add more options...
});
```

### CommonMark Options

Edit the converter settings in `MarkdownEditor.php`:

```php
$this->markdownConverter = new CommonMarkConverter([
    'html_input' => 'strip',        // Strip unsafe HTML
    'allow_unsafe_links' => false,  // Disallow javascript: links
    // Add more options...
]);
```

## Security Considerations

1. **HTML Stripping**: User HTML is stripped to prevent XSS attacks
2. **Link Safety**: JavaScript protocol links are disabled
3. **Permission Checks**: All save operations verify `edit posts` permission
4. **Validation**: Content is validated (max 50,000 characters)
5. **Caching**: HTML output is cached to reduce processing overhead

## Troubleshooting

### Editor Not Loading

1. Check browser console for JavaScript errors
2. Verify EasyMDE CDN is accessible
3. Ensure Alpine.js is initialized

### Auto-save Not Working

1. Check network tab for API requests
2. Verify user has `edit posts` permission
3. Check Laravel logs for errors

### Preview Not Updating

1. Verify `wire:model.live.debounce` is working
2. Check browser console for Livewire errors
3. Ensure CommonMark is installed: `composer require league/commonmark`

## Future Enhancements

- [ ] Collaborative editing with WebSockets
- [ ] Version history for content changes
- [ ] Drag-and-drop image upload
- [ ] Custom markdown syntax extensions
- [ ] Export to PDF/Word
- [ ] Import from external sources
