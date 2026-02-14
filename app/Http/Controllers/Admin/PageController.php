<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Page\StorePageRequest;
use App\Http\Requests\Page\UpdatePageRequest;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Admin Page Controller
 *
 * Handles CRUD operations for static pages in the admin panel.
 */
class PageController extends Controller
{
    /**
     * Display a listing of pages.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Page::class);

        $status = $request->get('status', 'all');

        $pages = Page::query()
            ->when($status !== 'all', function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        $counts = [
            'all' => Page::count(),
            'published' => Page::published()->count(),
            'draft' => Page::draft()->count(),
        ];

        return view('admin.pages.index', compact('pages', 'counts', 'status'));
    }

    /**
     * Show the form for creating a new page.
     */
    public function create(): View
    {
        $this->authorize('create', Page::class);

        return view('admin.pages.create');
    }

    /**
     * Store a newly created page.
     */
    public function store(StorePageRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['author_id'] = $request->user()->id;

        $page = Page::create($validated);

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Page created successfully.');
    }

    /**
     * Display the specified page.
     */
    public function show(Page $page): View
    {
        $this->authorize('view', $page);

        return view('admin.pages.show', compact('page'));
    }

    /**
     * Show the form for editing the specified page.
     */
    public function edit(Page $page): View
    {
        $this->authorize('update', $page);

        return view('admin.pages.edit', compact('page'));
    }

    /**
     * Update the specified page.
     */
    public function update(UpdatePageRequest $request, Page $page): RedirectResponse
    {
        $validated = $request->validated();

        $page->update($validated);

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Page updated successfully.');
    }

    /**
     * Remove the specified page.
     */
    public function destroy(Page $page): RedirectResponse
    {
        $this->authorize('delete', $page);

        $page->delete();

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'Page deleted successfully.');
    }
}
