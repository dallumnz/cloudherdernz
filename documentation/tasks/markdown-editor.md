# Task: Markdown Editor Implementation

## Overview
Implement a markdown editor for post authoring in CloudHerder, storing content in the database and serving rendered markdown to visitors.

## Requirements

### Editor Component
- Use EasyMDE (https://easymde.com/) - confirmed as good choice
- Live preview pane
- Toolbar for common formatting (bold, italic, links, headers, lists, code blocks)
- Image upload/drag-drop support (integrate with existing media library)
- Full-screen writing mode
- Auto-save draft functionality

### Storage
- Content stored in database `posts.content` field (already exists)
- Parse markdown to HTML on render (cache result)
- Based on: https://www.honeybadger.io/blog/markdown-laravel/

### Integration Points
- Update PostManager Livewire component
- Update Post model to handle markdown rendering
- Ensure featured images, galleries still work with markdown content
- Newsletter posts should also support markdown

## Deliverables
1. EasyMDE integrated in post editor
2. Markdown rendered to HTML when displaying posts
3. Existing post functionality preserved
4. Works with all post types (Standard, Image, Video, Audio, Newsletter)

## Status
- Assigned: Agent Agency
- Priority: High
- Blocks: CloudHerder v1.0 release

## Notes
- This makes CloudHerder feature-complete for v1.0
- Will be standard feature in generic CMS
