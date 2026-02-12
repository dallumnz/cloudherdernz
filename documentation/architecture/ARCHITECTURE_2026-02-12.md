# CloudHerder NZ Architecture

**Date:** 2026-02-13
**Feature:** Sitemap Generation

## Implementation Plan

### 1. Requirements Summary
- Expose `/sitemap.xml` route returning XML sitemap.
- Include URLs for posts, categories, and tags.
- Follow standard protocol: `xmlns`, `<lastmod>`, `<changefreq>`, `<priority>`.
- Cache the generated sitemap for 1 hour.
- Provide unit/feature tests covering generation logic and HTTP response.

### 2. Database Schema Changes
No new tables required; existing `posts`, `categories`, `tags` tables are sufficient.

### 3. Models Required
Existing models:
| Model | Purpose |
|-------|---------|
| Post | Blog posts |
| Category | Post categories |
| Tag | Post tags |

No new models needed.

### 4. Controllers & Routes
- **Route**: `GET /sitemap.xml` → `SitemapController@index`
- **Controller**: `app/Http/Controllers/SitemapController.php`
  - Delegates to a dedicated service for XML generation.

### 5. Service Layer
- **Service**: `app/Services/SitemapGenerator.php`
  - Builds the sitemap XML string.
  - Pulls latest updated timestamps from posts, categories, tags.
  - Applies changefreq and priority heuristics.
  - Caches result using Laravel Cache (1 hour).

### 6. Tests
- **Unit Test**: `tests/Unit/SitemapGeneratorTest.php`
  - Verify XML structure, correct URLs, timestamps, caching behavior.
- **Feature Test**: `tests/Feature/SitemapRouteTest.php`
  - Hit `/sitemap.xml`, assert status 200, content type `application/xml` and presence of expected tags.

### 7. Edge Cases & Validation
- Handle empty tables gracefully (no URLs).
- Ensure timestamps are ISO8601 compliant.
- Avoid duplicate URLs if a post belongs to multiple categories/tags.
- Cache invalidation: manual flush via artisan command if needed.

### 8. Integration Points
- No new migrations required.
- Service will use existing Eloquent models; no changes to relationships.
- Route will be added to `routes/web.php` (or a dedicated sitemap route file). 
- Cache key: `sitemap_xml`. 
- Use Laravel's `Cache::remember()` for 1 hour.

### 9. Next Steps
1. Create `SitemapGenerator` service with XML building logic.
2. Implement `SitemapController@index` to return response with appropriate headers.
3. Add route `/sitemap.xml` pointing to controller.
4. Write unit tests for generator and feature test for route.
5. Run tests, ensure coverage passes.
6. Commit changes and review.

---

**Note:** All code will follow existing coding standards (PSR‑12, Laravel 12 conventions). No new migrations or models are required for this feature.
