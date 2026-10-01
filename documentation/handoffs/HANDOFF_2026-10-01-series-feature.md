# Handoff: Series Feature

**Date:** 2026-10-01 NZDT  
**Commit:** `cd98850 feat: add Series as first-class content type`  
**Follow-up commits:** `d108aa0 feat: add series dropdown to post manager`, `173cd95 chore: npm audit fix`  
**Status:** Deployed to production  
**Backup:** `/home/dallum/Backups/cloudherder_production_20261001_160816.dump` (3.2 MB)

---

## What was done

### 1. Series as a first-class content type

Series are no longer just pages or tags. They have their own model, admin UI, public landing page, and a link to posts via a taxonomy term.

### 2. Files added / changed

| Path | Change |
|------|--------|
| `app/Models/Series.php` | New model with SEO, media, markdown description, publish/draft helpers |
| `app/Livewire/SeriesManager.php` | Admin CRUD Livewire component |
| `app/Livewire/PostManager.php` | Added series dropdown to post create/edit form |
| `app/Http/Controllers/PublicSeriesController.php` | Public series display controller |
| `resources/views/livewire/series-manager.blade.php` | Admin UI |
| `resources/views/livewire/post-manager.blade.php` | Added series `<flux:select>` dropdown |
| `resources/views/series/show.blade.php` | Public series landing page; added featured image display |
| `database/migrations/2026_10_01_151620_create_series_table.php` | Series table |
| `database/factories/SeriesFactory.php` | Test factory |
| `database/seeders/RolePermissionSeeder.php` | Added `view/create/edit/delete series` permissions |
| `resources/views/layouts/app/sidebar.blade.php` | Added Series nav item with badge count |
| `routes/web.php` | Added `admin/series` route |
| `routes/public/web.php` | Added `/series/{slug}` public route |
| `tests/Feature/SeriesTest.php` | Feature tests |
| `package-lock.json` | `npm audit fix` — resolved 13 transitive vulnerabilities |

### 3. Database migration

Migration `2026_10_01_151620_create_series_table` ran on both local and production.

### 4. Permissions

Admins and Editors now have series permissions. Seeded on production with:

```bash
php artisan db:seed --class=RolePermissionSeeder --force
```

### 5. Deploy

- Backup taken before deploy
- `git pull origin main`
- `npm ci && npm run build`
- `php artisan migrate --force`
- `php artisan optimize:clear && php artisan optimize`
- Verified home returns 200, `/admin/series` redirects to login (auth protected), `/series/{slug}` returns 404 for non-existent slug (expected)

---

## How to use it

1. Log into `/admin`.
2. Click **Series** in the sidebar.
3. Create a series:
   - Title, slug, tagline
   - Markdown description (via `livewire-markdown-editor`)
   - Status: Draft or Published
   - Series tag: pick or create a taxonomy term under the `series` taxonomy
   - Featured image (optional)
4. Save.
5. Tag posts with that series term. They will appear on the public series page automatically.
6. Visit `/series/{slug}`.

---

## What's left / next steps

1. **Main navigation integration (optional)**
   - Decide if series should appear in the public main nav.
   - Currently they are only reachable via `/series/{slug}` or links from posts/pages.

2. **Media Library picker for markdown (optional)**
   - The markdown editor already handles image uploads via its toolbar.
   - A separate "insert from Media Library" button could be added later if desired.

3. **SEO / sitemap**
   - Series public pages are not yet in `sitemap.xml`.
   - Add series URLs to the sitemap and verify SEO meta renders correctly.

4. **Tests**
   - Series tests pass. Broader test suite has pre-existing failures unrelated to this feature (RouteNotFound, DateTime, taxonomy query issues).

5. **Digest cron timezone**
   - Daylight saving change on 2026-09-27 shifted NZDT; digest did not run on the expected schedule on 2026-10-01. Review cron timing.

---

## Notes

- Public series controller resolves by slug and only shows published series.
- Draft series return 404 publicly.
- Series posts are pulled from the linked taxonomy term, ordered by `published_at` ascending.
- The `description_html` accessor caches rendered markdown for 24 hours.
- Posts must have both `status = published` and a non-null `published_at` to appear on the series page. If a post shows as "published" in the admin but is missing from the series page, check the `published_at` timestamp.
