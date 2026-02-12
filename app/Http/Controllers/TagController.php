<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tag\StoreTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;
use App\Models\Taxonomy;
use App\Models\TaxonomyTerm;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TagController extends Controller
{
    public function index(): View
    {
        $tagTaxonomy = Taxonomy::query()
            ->where('type', 'tag')
            ->first();

        $tags = TaxonomyTerm::query()
            ->when($tagTaxonomy, fn ($q) => $q->where('taxonomy_id', $tagTaxonomy->id))
            ->with('taxonomy')
            ->latest()
            ->paginate(20);

        return view('tags.index', compact('tags'));
    }

    public function show(TaxonomyTerm $tag): View
    {
        $tag->load(['taxonomy', 'posts']);

        return view('tags.show', compact('tag'));
    }

    public function create(): View
    {
        return view('tags.create');
    }

    public function store(StoreTagRequest $request): RedirectResponse
    {
        $this->authorize('create', TaxonomyTerm::class);

        $validated = $request->validated();

        // Get or create the tag taxonomy
        $tagTaxonomy = Taxonomy::query()
            ->firstOrCreate(
                ['type' => 'tag'],
                [
                    'name' => 'Tags',
                    'slug' => 'tags',
                    'description' => 'Content tags',
                    'is_hierarchical' => false,
                ]
            );

        $validated['taxonomy_id'] = $tagTaxonomy->id;
        $validated['parent_id'] = null; // Tags are not hierarchical

        $tag = TaxonomyTerm::create($validated);

        return redirect()
            ->route('tags.index')
            ->with('success', "Tag '{$tag->name}' created successfully.");
    }

    public function edit(TaxonomyTerm $tag): View
    {
        $this->authorize('update', $tag);

        return view('tags.edit', compact('tag'));
    }

    public function update(UpdateTagRequest $request, TaxonomyTerm $tag): RedirectResponse
    {
        $this->authorize('update', $tag);

        $validated = $request->validated();

        $tag->update($validated);

        return redirect()
            ->route('tags.index')
            ->with('success', "Tag '{$tag->name}' updated successfully.");
    }

    public function destroy(TaxonomyTerm $tag): RedirectResponse
    {
        $this->authorize('delete', $tag);

        $name = $tag->name;
        $tag->delete();

        return redirect()
            ->route('tags.index')
            ->with('success', "Tag '{$name}' deleted successfully.");
    }
}
