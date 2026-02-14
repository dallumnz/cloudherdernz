# CloudHerder Newsletter Subscribers Management

**Date:** 2026-02-13
**Feature:** Newsletter Subscribers Management

## Implementation Plan

### 1. Database Schema
Create migration `create_newsletter_subscribers_table` with columns:
- id (bigIncrements)
- email (string, unique, validated format)
- name (string, nullable)
- subscribed_at (timestamp, nullable)
- unsubscribed_at (timestamp, nullable)
- status (enum: pending, active, unsubscribed) default pending
- deleted_at (soft delete timestamp)
- timestamps

### 2. Model
`app/Models/NewsletterSubscriber.php`
- Use `SoftDeletes`, `$fillable`, `$casts`, validation rules.
- Define scopes for active, pending, unsubscribed.

### 3. Admin CRUD
- Resource controller `app/Http/Controllers/Admin/NewsletterSubscriberController.php` with index, create, store, edit, update, destroy.
- FormRequest classes for validation.
- Routes: `Route::prefix('admin')->name('admin.')->group(fn() => Route::resource('newsletter-subscribers', NewsletterSubscriberController::class));`
- Views using Flux UI components (buttons, tables).

### 4. Public Endpoints
- API routes in `routes/api.php`:
  - POST `/api/newsletter/subscribe` → `NewsletterSubscriptionController@store`
  - DELETE `/api/newsletter/unsubscribe/{email}` → `NewsletterSubscriptionController@destroy`
- Controllers validate email, check uniqueness, set status.
- Use Sanctum or API token middleware for protected admin routes.

### 5. Validation & Business Rules
- Email must be unique in table (including soft‑deleted records).
- Format validated via regex and `Email` rule.
- On subscribe: create record with status `active`, set `subscribed_at`.
- On unsubscribe: update status to `unsubscribed`, set `unsubscribed_at`.

### 6. Soft Delete
- Admin delete performs soft delete; records remain for audit.
- Query scopes exclude soft‑deleted by default.

### 7. Seeding
- Seeder `NewsletterSubscriberSeeder` creates a few demo subscribers and an admin user with role `newsletter_admin`.
- Register seeder in `DatabaseSeeder.php`.

### 8. Tests
- Feature tests for public subscribe/unsubscribe endpoints.
- Unit tests for model scopes, validation rules.
- Admin CRUD tests ensuring permissions.

### 9. Documentation & ADRs
- Add an Architecture Decision Record (ADR‑001) documenting the choice of soft deletes and enum status.
- Update README with API usage examples.

---

**Next Steps**
1. Create migration and model.
2. Implement admin CRUD controller, requests, routes, and views.
3. Add public API controllers and routes.
4. Write seeders and run `php artisan db:seed`.
5. Develop tests and run coverage.
6. Commit changes with clear message "Add Newsletter Subscribers feature".