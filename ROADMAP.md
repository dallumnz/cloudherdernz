# CloudHerder Roadmap

## Newsletter System

### Completed ✅
- [x] Subscribe form on homepage
- [x] Double opt-in confirmation flow
- [x] Admin subscriber management
- [x] "View in browser" page (`/newsletter/{id}`)
- [x] UUID support for NewsletterPost

### To Build 📋
- [ ] Newsletter email template
- [ ] "View in browser" link in emails
- [ ] Send newsletter to subscribers (queue job)
- [ ] Track opens/clicks
- [ ] Unsubscribe flow in emails

## Frontend

### Completed ✅
- [x] Homepage redesign
- [x] Posts list page
- [x] Single post view
- [x] Light/dark mode
- [x] Theme toggle
- [x] Category archive page (`/category/{slug}`)
- [x] Tag archive page (`/tag/{slug}`)
- [x] Search page
- [x] Contact page

### To Build 📋
- [ ] Pagination and filtering refinements on archive/search pages
- [ ] RSS feed improvements

## CMS Core Enhancements

### Publishing Workflow
- [ ] Scheduled publishing via queue job (flip `draft` → `published` at `published_at`)

### Media Library
- [ ] Media folders/collections beyond Spatie's default collections
- [ ] Media renaming from the admin UI

### Engagement
- [ ] Polls
- [ ] Embeddable CTAs

### Monitoring & Observability
- [ ] Error tracking integration (Sentry/Flare/Laravel Nightwatch)
- [ ] Uptime monitoring (Oh Dear/UptimeMate/Pingdom)
- [ ] Admin dashboard: recent errors, failed jobs, queue health
- [ ] Health-check endpoint (`/up` or dedicated status route)

### Operations
- [ ] Backup/restore Artisan commands (database + storage)
- [ ] Optional health-check/status Artisan command

## SEO & Discoverability

### Completed ✅
- [x] Sitemap generation (`sitemap.xml`)
- [x] `robots.txt`
- [x] Per-post SEO title/meta description backfill

### To Build 📋
- [ ] Canonical host enforcement (non-www / www redirect)
- [ ] Open Graph / Twitter card meta improvements

## Demo Priorities

- [ ] Add sample images for posts
- [ ] Test contact form end-to-end

---

*Last updated: 2026-10-09*
