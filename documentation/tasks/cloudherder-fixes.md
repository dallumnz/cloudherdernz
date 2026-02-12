# CloudHerder: Fix Polymorphic Issues & PostgreSQL Migration

## Project Context

CloudHerder is a Laravel 12 CMS with polymorphic posts (ImagePost, VideoPost, AudioPost).

**Current Status:**
- 53 failing tests (polymorphic schema issues)
- Tests still expect old `post_type_id` FK schema
- `.env` set to PostgreSQL (`DB_CONNECTION=pgsql`)

## Task 1: Fix Failing Tests (Priority)

### The Problem
Tests are failing because they expect `post_type_id` foreign key but we now use polymorphic relationships (`postable_type`, `postable_id`).

### Files Needing Updates
Search for tests using `post_type_id`:
```
tests/Feature/Api/PostApiTest.php
tests/Feature/Frontend/FrontendStackTest.php
tests/Feature/TaxonomyTest.php
database/factories/PostFactory.php
```

### What to Do
1. Update factories to use polymorphic `postable_type` / `postable_id`
2. Update test assertions to expect polymorphic columns
3. Create any missing ImagePost/VideoPost/AudioPost factories
4. Run `php artisan test` to verify all tests pass

## Task 2: Verify PostgreSQL Only

### Current State
- `.env` shows `DB_CONNECTION=pgsql`
- `config/database.php` default is still `'sqlite'`

### What to Do
1. Check if SQLite files exist (should be removed):
   - `database/database.sqlite`
   - Any references in migrations
2. Run `php artisan tinker` to verify connection:
   ```php
   DB::connection()->getPdo(); // Should not throw
   DB::connection()->getDatabaseName(); // Should show 'cloudherder'
   ```
3. Remove any SQLite-specific code from `config/database.php`

## Task 3: Review Cache Strategy

### Current Implementation
- `app/Http/Middleware/CacheApiResponses.php` (exists)
- `config/ai.php` has cache settings
- `.env` has `CACHE_STORE=redis`, `AI_VECTOR_CACHE_ENABLED=true`

### What to Do
1. Review the cache middleware for correctness
2. Verify Valkey/Redis connection works:
   ```bash
   valkey-cli ping
   ```
3. Check if there are any unused cache configurations
4. Ensure cache keys have proper prefixes to avoid collisions

## Deliverables
- All 53+ tests passing
- Only PostgreSQL connection in use
- Cache strategy documented or improved

## Commands to Use
```bash
# Run tests
php artisan test

# Check DB connection
php artisan tinker --execute="echo DB::connection()->getDatabaseName();"

# Verify Redis/Valkey
valkey-cli ping
```

## Important
- Do NOT modify the PostType enum or polymorphic relationships (they're correct)
- Focus ONLY on fixing tests and verifying PostgreSQL
- Remove orphaned code only (like PostTypeController if it's no longer needed)
