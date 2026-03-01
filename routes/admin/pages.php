<?php

use App\Http\Controllers\Admin\PageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Pages Routes
|--------------------------------------------------------------------------
|
| These routes handle CRUD operations for static pages in the admin panel.
| All routes require authentication and appropriate permissions.
|
*/

Route::middleware(['auth', 'permission:view pages'])->prefix('admin/pages')->name('admin.pages.')->group(function (): void {
    Route::get('/', [PageController::class, 'index'])->name('index');
    Route::get('/create', [PageController::class, 'create'])->name('create');
    Route::post('/', [PageController::class, 'store'])->name('store');
    Route::get('/{page}', [PageController::class, 'show'])->name('show');
    Route::get('/{page}/edit', [PageController::class, 'edit'])->name('edit');
    Route::put('/{page}', [PageController::class, 'update'])->name('update');
    Route::delete('/{page}', [PageController::class, 'destroy'])->name('destroy');
});
