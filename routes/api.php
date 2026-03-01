<?php

use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\NewsletterSubscriptionController;
use App\Http\Controllers\Api\PostApiController;
use App\Http\Controllers\Api\TaxonomyApiController;
use App\Http\Controllers\Api\TaxonomyTermApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider and assigned to the
| "api" middleware group. They are stateless and return JSON responses.
|
*/

// Public API routes (no authentication required)
Route::prefix('v1')->group(function (): void {

    // Newsletter subscription endpoints
    Route::post('/newsletter/subscribe', [NewsletterSubscriptionController::class, 'subscribe'])
        ->name('api.newsletter.subscribe');

    Route::get('/newsletter/confirm/{token}', [NewsletterSubscriptionController::class, 'confirm'])
        ->name('api.newsletter.confirm');

    Route::post('/newsletter/unsubscribe', [NewsletterSubscriptionController::class, 'unsubscribe'])
        ->name('api.newsletter.unsubscribe');

    Route::get('/newsletter/status', [NewsletterSubscriptionController::class, 'status'])
        ->name('api.newsletter.status');

    // Post endpoints - Public (read-only)
    Route::get('/posts', [PostApiController::class, 'index'])
        ->name('api.posts.index');

    Route::get('/posts/search', [PostApiController::class, 'search'])
        ->name('api.posts.search');

    Route::get('/posts/type/{type}', [PostApiController::class, 'byType'])
        ->name('api.posts.by-type');

    Route::get('/posts/{post}', [PostApiController::class, 'show'])
        ->name('api.posts.show');

    // Taxonomy endpoints (tags and categories)
    Route::get('/taxonomies', [TaxonomyApiController::class, 'index'])
        ->name('api.taxonomies.index');

    Route::get('/taxonomies/{taxonomy}', [TaxonomyApiController::class, 'show'])
        ->name('api.taxonomies.show');

    Route::get('/terms', [TaxonomyTermApiController::class, 'index'])
        ->name('api.terms.index');

    Route::get('/terms/{taxonomy_term}', [TaxonomyTermApiController::class, 'show'])
        ->name('api.terms.show');

    Route::get('/terms/{taxonomy_term}/posts', [TaxonomyTermApiController::class, 'posts'])
        ->name('api.terms.posts');
});

// Protected API routes (authentication required)
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function (): void {

    // Post management endpoints (require authentication and appropriate permissions)
    Route::post('/posts', [PostApiController::class, 'store'])
        ->name('api.posts.store')
        ->middleware('permission:create posts');

    Route::put('/posts/{post}', [PostApiController::class, 'update'])
        ->name('api.posts.update')
        ->middleware('permission:edit posts');

    Route::patch('/posts/{post}/content', [PostApiController::class, 'updateContent'])
        ->name('api.posts.update-content')
        ->middleware('permission:edit posts');

    Route::delete('/posts/{post}', [PostApiController::class, 'destroy'])
        ->name('api.posts.destroy')
        ->middleware('permission:delete posts');

    // Comment endpoints
    Route::get('{type}/{id}/comments', [CommentController::class, 'index'])
        ->name('api.comments.index');

    Route::post('{type}/{id}/comments', [CommentController::class, 'store'])
        ->name('api.comments.store');

    Route::get('comments/{comment}', [CommentController::class, 'show'])
        ->name('api.comments.show');

    Route::put('comments/{comment}', [CommentController::class, 'update'])
        ->name('api.comments.update');

    Route::delete('comments/{comment}', [CommentController::class, 'destroy'])
        ->name('api.comments.destroy');

    // Admin-only endpoints
    Route::middleware(['permission:view posts'])->group(function (): void {
        // Additional admin endpoints can be added here
        // e.g., Route::get('/admin/posts', [PostApiController::class, 'adminIndex']);
    });
});
