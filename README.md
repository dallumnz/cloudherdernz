# CloudHerder NZ

A self-hostable, polymorphic content CMS built with Laravel 12, Livewire, and Flux. Designed for independent publishers who want first-class support for articles, audio, video, newsletters, and galleries without surrendering their platform to a SaaS silo.

## Project Overview

CloudHerder NZ powers [cloudherder.nz](https://cloudherder.nz). It treats content as structured, typed posts rather than a single blob, making it easier to publish, syndicate, and reuse content across different surfaces and channels over time.

## Features

- **Polymorphic Post Types** — Standard articles, image galleries, video posts, audio posts, and newsletter posts each get their own data shape and presentation.
- **Taxonomy & Series** — Tags, categories, and series for grouping related content.
- **Media Library** — Image uploads and conversions via Spatie MediaLibrary, with featured images and dedicated gallery collections.
- **SEO** — Sitemap, robots.txt, per-post SEO meta titles and descriptions via `ralphjsmit/laravel-seo`.
- **Search** — Laravel Scout-backed search.
- **Comments** — Built-in comment threads.
- **Analytics** — Request-level analytics via `me-shaon/laravel-request-analytics`.
- **Activity Logging** — Model event logging via `spatie/laravel-activitylog`.
- **Admin UI** — Livewire + Flux management interface for posts, media, taxonomy, users, and settings.
- **Authentication** — Laravel Fortify with role/permission scaffolding.
- **RSS & Sitemap** — Public feeds for syndication and search indexing.

## Requirements

- PHP ^8.2
- Composer
- Node.js + npm
- SQLite, MySQL, or PostgreSQL
- (Optional) Redis for cache/queues/sessions

## Manual Deployment

### 1. Clone and install dependencies

```bash
git clone https://github.com/dallumnz/cloudherdernz.git
cd cloudherdernz
composer install
npm install
```

### 2. Environment and app key

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` to set your database, app URL, mail driver, and any third-party credentials.

### 3. Database and storage

```bash
php artisan migrate --force
php artisan storage:link
```

Seed roles and permissions if a seeder is available for your environment:

```bash
php artisan db:seed --class=RolePermissionSeeder
```

### 4. Build assets

```bash
npm run build
```

### 5. Serve

For local development:

```bash
php artisan serve
npm run dev
```

For production, point your web server at the `public/` directory and ensure the web user can write to `storage/` and `bootstrap/cache/`.

### Docker / Laravel Sail

If you prefer containers, Laravel Sail is included:

```bash
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --force
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

See [Laravel Sail documentation](https://laravel.com/docs/sail) for details.

## Running Tests

```bash
composer test
```

Or run Pest directly:

```bash
./vendor/bin/pest
```

## Contributing

This is a personal project. Issues and pull requests are welcome, but please open an issue before submitting larger changes so we can agree on direction first.

## License

MIT. See [LICENSE](LICENSE).
