<?php

use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\PublicSeriesController;
use Illuminate\Support\Facades\Route;

// Public Page Routes - uses explicit binding to resolve only published pages with eager loaded author
Route::get('/page/{page:slug}', [PublicPageController::class, 'show'])
    ->name('pages.show')
    ->where('slug', '[a-zA-Z0-9_-]+');

// Public Series Routes
Route::get('/series/{slug}', [PublicSeriesController::class, 'show'])
    ->name('series.show')
    ->where('slug', '[a-zA-Z0-9_-]+');
