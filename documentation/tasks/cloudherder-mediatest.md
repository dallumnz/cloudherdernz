# CloudHerder: Fix MediaLibraryTest Stub Tests

## Project Context

CloudHerder is a Laravel 12 CMS with polymorphic posts and Spatie Media Library.

**Current Status:**
- All taxonomy and post tests passing (60+ tests)
- `RolePermissionSeeder` exists with media permissions (`view media`, `upload media`, `delete media`)
- `MediaLibraryTest.php` is incomplete (stub tests with `// User implements:` comments)

## Task: Complete MediaLibraryTest Implementation

### The Problem
The `MediaLibraryTest.php` file contains stub tests that:
1. Create users without assigning roles/permissions
2. Have comments like `// User implements:` instead of actual assertions
3. Tests expect `view media` permission but users aren't assigned permissions
4. Tests aren't seeding the database before running

### What to Do

1. **Seed the database before tests**:
   ```php
   beforeEach(function () {
       $this->seed(RolePermissionSeeder::class);
       // ... rest of setup
   });
   ```

2. **Assign roles/permissions to test users**:
   ```php
   $admin = User::factory()->create();
   $admin->assignRole('Admin');  // Admin has all permissions
   ```

3. **Complete the stub tests** — Replace comments with actual assertions:
   - `can seed media library permissions` → Run seeder, verify permissions exist
   - `has required media permissions defined` → Assert each permission exists
   - `can upload a featured image` → Assert upload success
   - `can upload multiple gallery images` → Assert all images uploaded
   - `validates image file types` → Assert validation errors
   - `validates image file size` → Assert validation errors
   - `requires authentication` → Assert redirect to login
   - `requires proper permissions` → Assert 403 forbidden
   - `deletes associated media` → Assert media deleted with post
   - `can delete individual media` → Assert media deleted
   - `removes files from storage` → Assert file removed
   - `generates featured image conversion` → Assert conversion exists
   - `generates thumbnail conversion` → Assert conversion exists
   - `allows admins to manage all media` → Assert admin has all permissions
   - `restricts media actions` → Assert permission-based access
   - `can access media library index` → Assert 200 OK
   - `requires media permission` → Assert 403 forbidden

4. **Run the tests**:
   ```bash
   php artisan test --filter MediaLibraryTest
   ```

### Files to Modify
```
tests/Feature/MediaLibraryTest.php
```

### Requirements
- All MediaLibraryTest tests should pass (remove stubs, add real assertions)
- Use `RolePermissionSeeder` to seed permissions before tests
- Assign appropriate roles to test users (Admin, Editor, etc.)
- Assert actual behavior (status codes, database state, storage files)

## Deliverables
- Complete all stub tests in `MediaLibraryTest.php`
- All 4 MediaLibraryTest tests passing
- Tests properly seed database and assign roles

## Commands to Use
```bash
# Run tests to verify
php artisan test --filter MediaLibraryTest

# Run all tests
php artisan test
```
