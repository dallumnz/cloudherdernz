# CloudHerderNZ

**Date:** 2026-02-15
**Feature:** TASK-search-frontend-2026-02-15

## Implementation Plan

### 1. Requirements Summary
- Add a search bar to the blog posts listing page.
- Debounced input (300 ms) that queries the existing API endpoint `/api/posts` with `?search=`.
- Paginated results, using Laravel's pagination and Livewire for dynamic updates.
- Accessible markup: proper labels, ARIA attributes, keyboard navigation.
- Use Flux UI components (`<flux:text-input>`, `<flux:button>`). 

### 2. Database Schema Changes
No schema changes required – the API already accepts a `search` query param and returns paginated posts.

### 3. Models / Controllers / Routes
- **API**: Existing `PostController@index` supports `search`. No change.
- **Frontend**: Create a Livewire component `SearchPosts` that:
  - Holds `$query`, `$page`, `$perPage`.
  - Listens to debounced input via Alpine.js or Livewire's `defer` + `wire:model.debounce.300ms`.
  - Calls the API using Laravel’s `Http::get('/api/posts', ['search'=>$this->query,'page'=>$this->page])` and stores results.
- **Routes**: No new routes; use existing `/posts` page to embed component.

### 4. Files to Create / Modify
| File | Purpose |
|------|---------|
| `app/Http/Livewire/SearchPosts.php` | Livewire component logic |
| `resources/views/livewire/search-posts.blade.php` | Component view using Flux UI components |
| `resources/views/posts/index.blade.php` | Include the component and pagination controls |
| `resources/css/app.css` | Add any necessary Tailwind utilities for layout |

### 5. Edge Cases & Accessibility
- Empty query → show all posts.
- No results → friendly message.
- Keyboard: input focus, enter key triggers search immediately.
- Screen readers: label the input and announce result count.

### 6. Testing Strategy
- Unit test Livewire component state changes.
- Feature test that simulates typing and verifies API calls and pagination.
- Accessibility audit using axe-core in a headless browser (optional).

## Next Steps
1. Create Livewire component & view.
2. Update posts index to embed it.
3. Add Tailwind styles if needed.
4. Write tests.
5. Run Pint, Pest and ensure CI passes.