# CloudHerder.nz - Client Deliverables

This folder contains architecture documentation and diagrams for the CloudHerder.nz CMS project.

## Folder Structure

```
architect/
├── architecture-diagram.drawio   # Layered architecture diagram
├── database-schema.drawio         # Database ERD schema
└── README.md                      # This file
```

## Diagrams

### 1. Architecture Diagram (`architecture-diagram.drawio`)

Shows the Laravel application architecture following the **Layered Architecture** pattern:

- **Presentation Layer** - Routes, Controllers, Form Requests, API Resources, Middleware
- **Application Layer** - Service classes (PostServices, MediaServices, TagServices, UserServices, PermissionServices)
- **Domain Layer** - Eloquent Models (User, Role, Permission, Post, ImagePost, VideoPost, AudioPost, Tag, Category, Media)
- **Infrastructure Layer** - Migrations, Factories, Seeders, Filesystem Config, Policy Classes

### 2. Database Schema (`database-schema.drawio`)

Complete ERD showing all database tables:

**Core Tables:**
- `users` - User accounts with authentication
- `roles` - Spatie Permission roles
- `permissions` - Spatie Permission permissions

**Content Tables:**
- `posts` - Main post table with polymorphic relationship
- `image_posts` - Image post type specific data
- `video_posts` - Video post type specific data
- `audio_posts` - Audio post type specific data

**Taxonomy Tables:**
- `tags` - Tag definitions
- `categories` - Hierarchical categories with parent_id

**Media Tables:**
- `media` - Spatie Media Library table (stores all media references)

## Opening Diagrams

### Option 1: DrawIO (Recommended)
1. Go to https://app.diagrams.net/
2. Click "Open Existing Diagram"
3. Upload the `.drawio` file

### Option 2: Desktop App
1. Download DrawIO Desktop from https://get.diagrams.net/
2. Open the `.drawio` files directly

### Option 3: VS Code
1. Install "Draw.io Integration" extension
2. Right-click the `.drawio` file and select "Open in Diagrams.net"

## Editable Formats

For editing, you can:
- Use the DrawIO web editor
- Export as PNG/SVG for presentations
- Export as PDF for documentation

## Key Architecture Decisions

1. **Polymorphic Posts** - Single `posts` table with `postable_type` and `postable_id` for ImagePost/VideoPost/AudioPost
2. **Spatie Permission** - Role-based access control with users, roles, and permissions
3. **Spatie Media Library** - Centralized media management for all content types
4. **Hierarchical Categories** - Categories can have parent categories (tree structure)

---

**Project:** CloudHerder.nz CMS
**Generated:** 2026-02-09
**Framework:** Laravel 12.x
