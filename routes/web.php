<?php

use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\NewsletterActivityController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RssFeedController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TaxonomyController;
use App\Http\Controllers\TaxonomyTermController;
use Illuminate\Support\Facades\Route;

// Sitemap Route
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// RSS Feed Route
Route::get('/feed', [RssFeedController::class, 'index'])->name('feed');

// Public Routes
Route::get('/', [HomeController::class, '__invoke'])->name('home');

// Public Post Routes
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/type/{type}', \App\Livewire\PostTypeFilter::class)->name('posts.by-type');
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

    // Admin Dashboard
    Route::get('admin', \App\Livewire\AdminDashboard::class)
        ->middleware('permission:view posts')
        ->name('admin.dashboard');

    // Post Management (Livewire)
    Route::get('admin/posts', \App\Livewire\PostManager::class)
        ->middleware('permission:view posts')
        ->name('admin.posts');

    // User Management (Livewire)
    Route::get('admin/users', \App\Livewire\UserManager::class)
        ->middleware('permission:view users')
        ->name('admin.users');

    // Comment Moderation (Livewire)
    Route::get('admin/comments', \App\Livewire\CommentModeration::class)
        ->middleware('permission:moderate comments')
        ->name('admin.comments');

    Route::resource('taxonomies', TaxonomyController::class);
    Route::resource('taxonomy-terms', TaxonomyTermController::class);

    // Tag Management
    Route::resource('tags', TagController::class)->except(['index', 'show']);

    // Category Management
    Route::resource('categories', CategoryController::class)->except(['index', 'show']);

    // Role Management (admin only)
    Route::get('roles/manage', \App\Livewire\RoleManager::class)
        ->middleware('permission:edit roles')
        ->name('roles.manage');

    // Tag Manager Livewire
    Route::get('admin/tags', \App\Livewire\TagManager::class)
        ->middleware('permission:view tags')
        ->name('admin.tags');

    // Category Manager Livewire
    Route::get('admin/categories', \App\Livewire\CategoryManager::class)
        ->middleware('permission:view categories')
        ->name('admin.categories');

    // Page Manager Livewire
    Route::get('admin/pages', \App\Livewire\PageManager::class)
        ->middleware('permission:view pages')
        ->name('admin.pages');

    // Analytics Dashboard (Livewire wrapper for laravel-request-analytics)
    Route::get('admin/analytics', \App\Livewire\AnalyticsWrapper::class)
        ->middleware('permission:view analytics')
        ->name('admin.analytics');

    // Media Library Routes
    Route::prefix('admin/media')->name('admin.media.')->middleware(['permission:view media'])->group(function () {
        Route::get('/', \App\Livewire\MediaUploader::class)->name('index');
        Route::get('/upload', \App\Livewire\MediaUploader::class)->name('upload');
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
    Route::get('posts/{post:id}/featured-image', \App\Livewire\FeaturedImageUploader::class)
        ->name('posts.featured-image')
        ->middleware('permission:edit posts');

    Route::get('posts/{post:id}/gallery', \App\Livewire\GalleryManager::class)
        ->name('posts.gallery')
        ->middleware('permission:edit posts');


});

Route::get('/subscribe/confirm/{token}', [\App\Http\Controllers\SubscribeController::class, 'confirm']);
require __DIR__.'/settings.php';
// Newsletter subscription routes
Route::get('/subscribe/confirm/{token}', [\App\Http\Controllers\SubscribeController::class, 'confirm'])
    ->name('subscribe.confirm');

// Newsletter web view (public)
Route::get('/newsletter/{id}', [\App\Http\Controllers\NewsletterViewController::class, 'show'])
    ->name('newsletter.show')
    ->where('id', '[0-9]+');

// Newsletter tracking pixel
Route::get('/newsletter/{id}/open', [\App\Http\Controllers\NewsletterViewController::class, 'trackOpen'])
    ->name('newsletter.track-open')
    ->where('id', '[0-9]+');

// Newsletter unsubscribe web
Route::get('/newsletter/unsubscribe', [\App\Http\Controllers\SubscribeController::class, 'showUnsubscribe'])
    ->name('newsletter.unsubscribe-web');
