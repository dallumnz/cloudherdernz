# Contact Form Implementation

**Task:** Implement Contact Form feature
**Project:** /home/dallum/projects/cloudherder.nz

## Files to Create

### 1. Migrations
- `database/migrations/YYYY_MM_DD_HHMMSS_create_contacts_table.php`
- `database/migrations/YYYY_MM_DD_HHMMSS_create_contact_blocklist_table.php`

### 2. Models
- `app/Models/Contact.php`
- `app/Models/ContactBlocklist.php`

### 3. Form Request
- `app/Http/Requests/ContactRequest.php`

### 4. Controllers
- `app/Http/Controllers/ContactController.php`
- `app/Http/Controllers/Admin/ContactController.php`

### 5. Views
- `resources/views/contact/show.blade.php`
- `resources/views/admin/inbox/index.blade.php`
- `resources/views/admin/inbox/show.blade.php`

## Implementation Details

### contacts table
```php
$table->id();
$table->string('name');
$table->string('email');
$table->string('subject')->nullable();
$table->text('message');
$table->enum('status', ['unread', 'read', 'archived'])->default('unread');
$table->timestamp('read_at')->nullable();
$table->ipAddress('sender_ip')->nullable();
$table->timestamps();
$table->softDeletes();
$table->index(['status', 'created_at']);
$table->index('email');
```

### contact_blocklist table
```php
$table->id();
$table->string('type')->comment('email or domain');
$table->string('value');
$table->text('reason')->nullable();
$table->timestamps();
$table->softDeletes();
$table->unique(['type', 'value']);
$table->index('type');
```

### ContactRequest validation
```php
'name' => 'required|string|max:255',
'email' => 'required|email|max:255',
'subject' => 'nullable|string|max:255',
'message' => 'required|string|max:5000',
'h-captcha-response' => 'required|hcaptcha',
```

### Routes to add to routes/web.php
```php
// Public
Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Admin (add to existing admin middleware group)
Route::prefix('admin/inbox')->name('admin.inbox.')->middleware(['auth', 'can:view contacts'])->group(function () {
    Route::get('/', [Admin\ContactController::class, 'index'])->name('index');
    Route::get('/{contact}', [Admin\ContactController::class, 'show'])->name('show');
    Route::put('/{contact}/read', [Admin\ContactController::class, 'markAsRead'])->name('read')
        ->middleware('permission:manage contacts');
    Route::put('/{contact}/spam', [Admin\ContactController::class, 'markAsSpam'])->name('spam')
        ->middleware('permission:manage contacts');
    Route::delete('/{contact}', [Admin\ContactController::class, 'destroy'])->name('destroy')
        ->middleware('permission:delete contacts');
});
```

## After Creating Files

1. Run migrations
2. Seed permissions (view contacts, delete contacts, manage contacts)
3. Write tests

## Output

List files created and run `php artisan test`.
