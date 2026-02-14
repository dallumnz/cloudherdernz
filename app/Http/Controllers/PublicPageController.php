<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Public Page Controller
 *
 * Handles displaying published static pages to the public.
 */
class PublicPageController extends Controller
{
    /**
     * Display the specified page by slug.
     *
     * Uses explicit resolution scoped to published pages with eager loaded author.
     *
     * @param  string  $slug  The page slug
     *
     * @throws NotFoundHttpException
     */
    public function show(string $slug): View
    {
        $page = Page::bySlug($slug)
            ->published()
            ->with('author')
            ->first();

        if (! $page) {
            throw new NotFoundHttpException('Page not found.');
        }

        return view('pages.show', compact('page'));
    }
}
