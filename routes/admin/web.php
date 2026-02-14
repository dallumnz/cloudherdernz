<?php

use App\Http\Controllers\Admin\NewsletterSubscriberController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| These routes are for administrative functions and require authentication
| with appropriate permissions.
|
*/

Route::middleware(['auth', 'permission:view newsletter subscribers'])->prefix('admin/newsletter-subscribers')->name('admin.newsletter-subscribers.')->group(function (): void {
    Route::get('/', [NewsletterSubscriberController::class, 'index'])->name('index');
    Route::get('/export', [NewsletterSubscriberController::class, 'export'])->name('export');
    Route::get('/{subscriber}', [NewsletterSubscriberController::class, 'show'])->name('show');
    Route::get('/{subscriber}/edit', [NewsletterSubscriberController::class, 'edit'])->name('edit');
    Route::put('/{subscriber}', [NewsletterSubscriberController::class, 'update'])->name('update');
    Route::delete('/{subscriber}', [NewsletterSubscriberController::class, 'destroy'])->name('destroy');

    // Status management
    Route::put('/{subscriber}/confirm', [NewsletterSubscriberController::class, 'confirm'])->name('confirm');
    Route::put('/{subscriber}/unsubscribe', [NewsletterSubscriberController::class, 'unsubscribe'])->name('unsubscribe');
    Route::put('/{subscriber}/reactivate', [NewsletterSubscriberController::class, 'reactivate'])->name('reactivate');
});
