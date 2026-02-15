# CloudHerder.nz Search Architecture

**Date:** 2026-02-15
**Feature:** Dedicated Search Results Page with Livewire & Flux UI

## 1. Overview
The new search feature introduces a dedicated results page that displays listings matching user queries, leveraging Laravel Scout for full‑text search and Livewire + Flux UI for an interactive frontend.

## 2. Architecture Decisions
| Decision | Option | Reasoning |
|----------|--------|-----------|
| Search Flow | Hybrid (suggestions dropdown → redirect to results page) | Keeps existing results page logic, provides instant feedback while typing, and preserves user expectations on the results page. |
| Frontend Technology | Livewire + Flux UI | Livewire allows server‑side reactivity without JavaScript; Flux UI gives ready‑made components that integrate with Tailwind. |
| Data Layer | Scout (Elasticsearch/Lucene) | Full‑text search across listings, scalable and performant. |
| API for Suggestions | `/api/search/suggestions` POST | Keeps suggestions lightweight (first 5 results), protects against over‑fetching. |

## 3. Routes
```php
// web.php
Route::get('/search', [SearchController::class, 'index'])->name('search.index');

// api.php
Route::post('/search/suggestions', [Api\SearchController::class, 'suggestions']);
```

## 4. Controllers
- **`SearchController`** – Handles web route, performs Scout search with optional type filter, paginates results, returns `resources/views/search/index.blade.php`.
- **`Api\SearchController`** – Returns JSON of up to 5 suggestions for Livewire component.

## 5. Views & Components
| Path | Purpose |
|------|---------|
| `resources/views/search/index.blade.php` | Results page template, paginated list, filter UI. |
| `app/Http/Livewire/SearchResults.php` | Livewire component that manages query, type, and pagination state; renders results via Blade view. |
| `resources/views/livewire/search-results.blade.php` | Component view using Flux UI components (`<flux:input>`, `<flux:list>`). |

## 6. Models / Repositories
- No new models. Use existing `Listing` model with Scout integration.
- Add a scope `scopeSearchByQuery($query, $term)` if not already present.

## 7. Migrations
If Scout is not yet configured for listings:
```php
Schema::table('listings', function (Blueprint $table) {
    $table->string('searchable_id')->nullable();
    $table->string('searchable_type')->nullable();
});
```
Run `php artisan scout:import Listing` after.

## 8. Tests
| Test | Location | Purpose |
|------|----------|---------|
| Feature test for search page | `tests/Feature/SearchTest.php` | Verify route, query handling, pagination, and no results case. |
| Livewire component test | `tests/Unit/Livewire/SearchResultsTest.php` | Ensure component renders, updates on input, and handles empty queries. |

## 9. File List to Create / Modify
| Path | Action |
|------|--------|
| `routes/web.php` | Add `/search` route |
| `routes/api.php` | Add `/api/search/suggestions` POST route |
| `app/Http/Controllers/SearchController.php` | New controller (if not existing) |
| `app/Http/Controllers/Api/SearchController.php` | New API controller |
| `app/Http/Livewire/SearchResults.php` | New Livewire component |
| `resources/views/livewire/search-results.blade.php` | Component view |
| `resources/views/search/index.blade.php` | Results page template |
| Migration for searchable columns (if needed) | `database/migrations/YYYY_MM_DD_HHMMSS_add_searchable_to_listings_table.php` |
| Tests | `tests/Feature/SearchTest.php`, `tests/Unit/Livewire/SearchResultsTest.php` |

## 10. Edge Cases & Validation
- Empty query → redirect to home or show all listings.
- No results → friendly message with suggestion to refine search.
- Invalid type parameter → ignore and default to all types.
- Rate limit API route (e.g., `ThrottleRequests`).

## 11. Integration Points
- Uses existing `Listing` model, Scout index, and Flux UI components.
- Livewire requires assets published (`livewire:publish --assets`).
- Ensure Tailwind CSS utilities are available for responsive design.

## Next Steps
1. Create migration if needed and run Scout import.
2. Implement controllers and routes.
3. Build Livewire component with Flux UI.
4. Write and run tests (`php artisan test --compact`).
5. Run Pint to format code.

---
*This architecture document is the source of truth for the search feature implementation.*