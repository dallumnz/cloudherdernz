# CloudHerder NZ

**Date:** 2026-02-13
**Feature:** Static Pages (About, Terms, Privacy Policy, etc.)

## Implementation Plan

### 1. Requirements Summary
- A `pages` table with columns: `id`, `title`, `slug` (unique), `content`, `status` (`draft`, `published`, `archived`), timestamps.
- Admin CRUD for pages via a dedicated controller and resource routes, protected by authentication & admin policy.
- Public route `/pages/{slug}` that resolves to the page if status is `published`; otherwise 404.
- Status workflow: draft → published → archived. Only `published` pages are visible publicly.
- Validation: unique slug, required title/content, status enum.

### 2. Database Schema Changes
```php
// database/migrations/2026_02_13_000000_create_pages_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->enum('status', ['draft','published','archived'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
}
```

### 3. Models Required
| Model | Location | Purpose |
|-------|----------|---------|
| Page | `app/Models/Page.php` | Eloquent model for static pages, casts status to enum and provides scopes.

**Page.php**
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Page extends Model
{
    protected $fillable = ['title','slug','content','status'];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }
}
```

### 4. Controllers Required
| Controller | Location | Methods |
|------------|----------|---------|
| PageController | `app/Http/Controllers/Admin/PageController.php` | index, create, store, edit, update, destroy (resource). Uses Form Requests for validation.
| PublicPageController | `app/Http/Controllers/PublicPageController.php` | show (by slug).

### 5. Routes Required
```php
// routes/admin/web.php
Route::middleware(['auth', 'admin'])
    ->prefix('pages')
    ->name('pages.')
    ->resource('', App\Http\Controllers\Admin\\PageController::class);

// routes/public/web.php
Route::get('/pages/{slug}', [App\Http\Controllers\PublicPageController::class, 'show'])->name('page.show');
```

### 6. Policies & Middleware
- `PagePolicy` in `app/Policies/PagePolicy.php` to restrict CRUD to admins.
- Admin middleware already exists (`admin`). If not, create a simple gate that checks `user->is_admin`.

### 7. Tests Required
| Test | Location | Purpose |
|------|----------|---------|
| PageModelTest | `tests/Unit/PageModelTest.php` | Ensure status enum and slug uniqueness.
| PageControllerTest | `tests/Feature/Admin/PageControllerTest.php` | CRUD flow, validation, status transitions.
| PublicPageRouteTest | `tests/Feature/PublicPageRouteTest.php` | 200 for published, 404 for draft/archived.

### 8. Edge Cases to Handle
- Duplicate slug: unique constraint + form request validation.
- Attempting to view non‑published page: return 404.
- Deleting a page that is referenced elsewhere (none in this feature).
- Status transition rules enforced via policy or business logic.

### 9. Integration Points
- Admin panel routes under `/admin/pages`.
- Blade templates for listing, editing pages; can reuse existing admin layout.
- Public view uses `resources/views/page/show.blade.php` with minimal styling.

---

**Next Steps**
1. Create migration and run `php artisan migrate`.
2. Implement Page model, scopes, casts.
3. Generate controllers via Artisan (`make:controller`).
4. Add Form Requests for validation.
5. Define routes in `routes/admin/web.php` and `routes/public/web.php`.
6. Create policies and register them.
7. Write tests and run `php artisan test --compact`.
8. Review code with Pint and Pest compliance.
