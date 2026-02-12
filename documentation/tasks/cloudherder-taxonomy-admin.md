# CloudHerder: Fix Taxonomy Issues + Admin Seeder

## Project Context

CloudHerder is a Laravel 12 CMS with polymorphic posts (ImagePost, VideoPost, AudioPost).

**Previous Session Completed:**
- ✅ Removed orphaned PostTypeController and PostTypeFactory
- ✅ Updated config/database.php default to pgsql
- ✅ Updated config/cache.php default to redis
- ✅ Fixed 55 post-related tests
- ✅ 53 failing tests → 55 passing (post tests)

**Outstanding Issues:**
- Taxonomy tests still failing (403 permission, 404 route issues)
- Need admin account seeder for browser testing

## Task 1: Fix Taxonomy Permission/Route Issues

### The Problem
Taxonomy tests (TagController, CategoryController) are failing with:
- 403 Unauthorized (permission issues)
- 404 Not Found (route issues)

### Files to Check
```
tests/Feature/TaxonomyTest.php
tests/Feature/TagController.php (if exists)
tests/Feature/CategoryController.php (if exists)
app/Http/Controllers/TagController.php
app/Http/Controllers/CategoryController.php
routes/web.php
```

### What to Do
1. Check routes in `routes/web.php` for tags/categories
2. Verify TagController and CategoryController have proper authorization
3. Check middleware on routes (should use policies or permission middleware)
4. Fix any issues found
5. Run `php artisan test` to verify TaxonomyTest passes

## Task 2: Create Admin Account Seeder

### Requirements
Create a database seeder for browser testing:

```php
name: 'Dallum'
email: 'dallum.brown@gmail.com'
password: 'p455w0rd'  // Will be hashed by Laravel
role: 'Admin'  // Should have all Spatie permissions
```

### What to Do
1. Create or update `database/seeders/AdminUserSeeder.php`
2. Use Laravel's Hash facade to hash the password
3. Assign 'Admin' role with all permissions
4. Run the seeder to create the user
5. Document the credentials for testing

### Example Code
```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

// Create Admin role if doesn't exist
$adminRole = Role::firstOrCreate(['name' => 'Admin'], [
    'guard_name' => 'web'
]);

// Create user
$user = User::create([
    'name' => 'Dallum',
    'email' => 'dallum.brown@gmail.com',
    'password' => Hash::make('p455w0rd'),
]);

// Assign role
$user->assignRole('Admin');
```

## Deliverables
- TaxonomyTest passes (all taxonomy-related tests)
- AdminUserSeeder.php created and tested
- Admin user can log in with credentials above

## Commands to Use
```bash
# Run taxonomy tests
php artisan test --filter TaxonomyTest

# Run specific test
php artisan test tests/Feature/TaxonomyTest.php

# Seed admin user
php artisan db:seed --class=AdminUserSeeder

# Verify user created
php artisan tinker --execute="User::where('email', 'dallum.brown@gmail.com')->first();"

# Run all tests
php artisan test
```

## Important Notes
- Use policies or Spatie Permission middleware for authorization
- Ensure routes follow Laravel naming conventions
- Test the admin login after seeding
