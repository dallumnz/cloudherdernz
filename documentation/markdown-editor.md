# Markdown Editor Integration

**Date:** 2026-03-01  
**Updated:** 2026-10-01  
**Status:** Implemented  
**Component:** `mckenziearts/livewire-markdown-editor` vendor package

## Overview

The Markdown Editor provides a rich editing experience for content using the `mckenziearts/livewire-markdown-editor` package. It supports live preview, image uploads, and GitHub-flavoured markdown rendering. Both `Post` and `Page` models use the same editor component.

## Features

- **Markdown Toolbar**: GitHub-style toolbar with headings, bold, italic, lists, links, code, quotes, and image upload
- **Live Preview**: Real-time HTML preview tab
- **Image Upload**: Direct image uploads inserted as markdown
- **Cached HTML Rendering**: Markdown to HTML conversion with 24-hour caching
- **Model Agnostic**: Used for both `Post` and `Page` content

## Package

- **Composer:** `mckenziearts/livewire-markdown-editor`
- **Component name:** `markdown-editor`
- **Alias tag:** `<livewire-markdown-editor>`

## Usage

### In a Blade form

```blade
<livewire-markdown-editor wire:model="content" placeholder="Write your content in Markdown..." />
```

### In Post Manager

```blade
{{-- resources/views/livewire/post-manager.blade.php --}}
<livewire-markdown-editor wire:model="content" placeholder="Write your post content in Markdown..." />
```

### In Page Manager

```blade
{{-- resources/views/livewire/page-manager.blade.php --}}
<livewire-markdown-editor wire:model="content" placeholder="Write your page content in Markdown..." />
```

## Model Rendering

### Post Model

The `Post` model includes accessors for rendering markdown:

```php
// Render content as HTML (cached for 24 hours)
$post->content_html;

// Render excerpt as HTML (cached for 24 hours)
$post->excerpt_html;
```

### Page Model

The `Page` model includes a `content_html` accessor that renders markdown to HTML with 24-hour caching:

```php
$page->content_html;
```

Cache is cleared automatically when the page is saved or deleted.

### Displaying Rendered Content

In your display views:

```blade
<article class="prose dark:prose-invert max-w-none">
    {!! clean($post->content_html) !!}
</article>
```

For pages:

```blade
<article class="prose dark:prose-invert max-w-none">
    {!! clean($page->content_html) !!}
</article>
```

## Configuration

Package config is published to `config/livewire-markdown-editor.php`.

Key defaults:

- `disk`: filesystem disk for uploads
- `upload.max_size`: 4096 KB
- `upload.allowed_extensions`: `jpg`, `jpeg`, `png`, `gif`, `webp`, `avif`
- `upload.images_only`: true
- `theme`: `github-light`

## Image Upload Integration

1. Click the image icon in the toolbar
2. Select an image file
3. The editor uploads it and inserts markdown: `![filename](url)`

## Security Considerations

1. **HTML Stripping**: User HTML is stripped during markdown conversion
2. **Link Safety**: JavaScript protocol links are disabled
3. **Validation**: Uploads are restricted to images with allowed extensions
4. **Caching**: HTML output is cached to reduce processing overhead
5. **Output Sanitization**: Use `clean()` when rendering HTML in Blade

## Troubleshooting

### Editor Not Loading

1. Check browser console for JavaScript errors
2. Verify `resources/js/admin.js` imports the package JS
3. Verify `resources/css/admin.css` imports the package CSS
4. Run `npm run build` if assets are out of date

### Preview Not Updating

1. Verify `wire:model` binding is correct
2. Check browser console for Livewire errors
3. Ensure `league/commonmark` is installed

## Historical Note

The project originally used a custom `App\Livewire\MarkdownEditor` component backed by EasyMDE. This was replaced by the vendor package to reduce maintenance burden and support reuse across `Post` and `Page` models. The custom component and its tests were removed on 2026-10-01.
