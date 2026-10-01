<?php

use App\Http\Controllers\Admin\AnalyticsExportController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\NewsletterActivityController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterViewController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RssFeedController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SubscribeController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TaxonomyController;
use App\Http\Controllers\TaxonomyTermController;
use App\Livewire\ActivityLogManager;
use App\Livewire\AnalyticsWrapper;
use App\Livewire\ApiTokenManager;
use App\Livewire\CategoryManager;
use App\Livewire\CommentModeration;
use App\Livewire\FeaturedImageUploader;
use App\Livewire\GalleryManager;
use App\Livewire\MediaUploader;
use App\Livewire\PageManager;
use App\Livewire\PostManager;
use App\Livewire\PostTypeFilter;
use App\Livewire\RoleManager;
use App\Livewire\SeriesManager;
use App\Livewire\TagManager;
use App\Livewire\UserManager;
use Illuminate\Support\Facades\Route;

// Sitemap Route
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// RSS Feed Route
Route::get('/feed', [RssFeedController::class, 'index'])->name('feed');

// Public Routes
Route::get('/', [HomeController::class, '__invoke'])->name('home');

// Public Post Routes
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/type/{type}', PostTypeFilter::class)->name('posts.by-type');
Route::get('/posts/{post}', [PostController::class, 'show'])
    ->name('posts.show');

// Search Routes
Route::get('/search', [SearchController::class, 'index'])
    ->name('search.index')
    ->middleware('throttle:search');

Route::get('/search/results', [SearchController::class, 'results'])
    ->name('search.results')
    ->middleware('throttle:search');

// Public Tag Routes
Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
Route::get('/tags/{tag}', [TagController::class, 'show'])
    ->name('tags.show')
    ->where('tag', '(?!create$|edit$)[a-zA-Z0-9_-]+'); // Exclude reserved words

// Public Category Routes
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show'])
    ->name('categories.show')
    ->where('category', '(?!create$|edit$)[a-zA-Z0-9_-]+'); // Exclude reserved words

// Contact Form
Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [ContactController::class, 'store'])
    ->name('contact.store')
    ->middleware('throttle:contact-submissions');

// Privacy Policy
Route::view('/privacy', 'privacy')->name('privacy');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::view('dashboard', 'dashboard')
        ->middleware(['verified'])
        ->name('dashboard');

    // Admin Dashboard (redirects to unified dashboard)
    Route::redirect('admin', 'dashboard')
        ->middleware('permission:view posts')
        ->name('admin.dashboard');

    // Post Management (Livewire)
    Route::get('admin/posts', PostManager::class)
        ->middleware('permission:view posts')
        ->name('admin.posts');

    // User Management (Livewire)
    Route::get('admin/users', UserManager::class)
        ->middleware('permission:view users')
        ->name('admin.users');

    // Comment Moderation (Livewire)
    Route::get('admin/comments', CommentModeration::class)
        ->middleware('permission:moderate comments')
        ->name('admin.comments');

    Route::resource('taxonomies', TaxonomyController::class);
    Route::resource('taxonomy-terms', TaxonomyTermController::class);

    // Tag Management
    Route::resource('tags', TagController::class)->except(['index', 'show']);

    // Category Management
    Route::resource('categories', CategoryController::class)->except(['index', 'show']);

    // Role Management (admin only)
    Route::get('roles/manage', RoleManager::class)
        ->middleware('permission:edit roles')
        ->name('roles.manage');

    // Tag Manager Livewire
    Route::get('admin/tags', TagManager::class)
        ->middleware('permission:view tags')
        ->name('admin.tags');

    // Category Manager Livewire
    Route::get('admin/categories', CategoryManager::class)
        ->middleware('permission:view categories')
        ->name('admin.categories');

    // Page Manager Livewire
    Route::get('admin/pages', PageManager::class)
        ->middleware('permission:view pages')
        ->name('admin.pages');

    // Series Manager Livewire
    Route::get('admin/series', SeriesManager::class)
        ->middleware('permission:view series')
        ->name('admin.series');

    // Analytics Dashboard (Livewire wrapper for laravel-request-analytics)
    Route::get('admin/analytics', AnalyticsWrapper::class)
        ->middleware('permission:view analytics')
        ->name('admin.analytics');

    Route::get('admin/analytics/export', [AnalyticsExportController::class, 'export'])
        ->middleware('permission:view analytics')
        ->name('admin.analytics.export');

    // Activity Log (Livewire)
    Route::get('admin/activity', ActivityLogManager::class)
        ->middleware('permission:view analytics')
        ->name('admin.activity');

    // API Token Management
    Route::get('admin/api-tokens', ApiTokenManager::class)
        ->middleware('permission:create posts')
        ->name('admin.api-tokens');

    // Media Library Routes
    Route::prefix('admin/media')->name('admin.media.')->middleware(['permission:view media'])->group(function () {
        Route::get('/', MediaUploader::class)->name('index');
        Route::get('/upload', MediaUploader::class)->name('upload');
    });

    // Contact Inbox
    Route::prefix('admin/inbox')->name('admin.inbox.')->middleware(['permission:view contacts'])->group(function () {
        Route::get('/', [AdminContactController::class, 'index'])->name('index');
        Route::get('/{contact}', [AdminContactController::class, 'show'])->name('show');
        Route::put('/{contact}/read', [AdminContactController::class, 'markAsRead'])->name('read')
            ->middleware('permission:manage contacts');
        Route::put('/{contact}/spam', [AdminContactController::class, 'markAsSpam'])->name('spam')
            ->middleware('permission:manage contacts');
        Route::delete('/{contact}', [AdminContactController::class, 'destroy'])->name('destroy')
            ->middleware('permission:delete contacts');
    });

    // Newsletter Activities (Admin)
    Route::prefix('admin/newsletters')->name('admin.newsletter-activities.')->middleware(['permission:view posts'])->group(function () {
        Route::get('/activities', [NewsletterActivityController::class, 'index'])->name('index');
        Route::get('/activities/create', [NewsletterActivityController::class, 'create'])->name('create');
        Route::post('/activities', [NewsletterActivityController::class, 'store'])->name('store');
        Route::get('/activities/{activity}', [NewsletterActivityController::class, 'show'])->name('show');
        Route::delete('/activities/{activity}', [NewsletterActivityController::class, 'destroy'])->name('destroy');
        Route::post('/activities/{activity}/retry', [NewsletterActivityController::class, 'retry'])->name('retry');
    });

    // Post Media Management (use :id to bypass slug route binding)
    Route::get('posts/{post:id}/featured-image', FeaturedImageUploader::class)
        ->name('posts.featured-image')
        ->middleware('permission:edit posts');

    Route::get('posts/{post:id}/gallery', GalleryManager::class)
        ->name('posts.gallery')
        ->middleware('permission:edit posts');

});

Route::get('/subscribe/confirm/{token}', [SubscribeController::class, 'confirm']);
require __DIR__.'/settings.php';
// Newsletter subscription routes
Route::get('/subscribe/confirm/{token}', [SubscribeController::class, 'confirm'])
    ->name('subscribe.confirm');

// Newsletter web view (public)
Route::get('/newsletter/{id}', [NewsletterViewController::class, 'show'])
    ->name('newsletter.show')
    ->where('id', '[0-9]+');

// Newsletter tracking pixel
Route::get('/newsletter/{id}/open', [NewsletterViewController::class, 'trackOpen'])
    ->name('newsletter.track-open')
    ->where('id', '[0-9]+');

// Newsletter unsubscribe web
Route::get('/newsletter/unsubscribe', [SubscribeController::class, 'showUnsubscribe'])
    ->name('newsletter.unsubscribe-web');
