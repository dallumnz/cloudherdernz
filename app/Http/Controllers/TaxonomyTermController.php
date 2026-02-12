<?php

namespace App\Http\Controllers;

use App\Models\Taxonomy;
use App\Models\TaxonomyTerm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaxonomyTermController extends Controller
{
    public function index(): View
    {
        $terms = TaxonomyTerm::query()
            ->with(['taxonomy', 'parent'])
            ->latest()
            ->paginate(20);

        return view('taxonomy-terms.index', compact('terms'));
    }

    public function create(): View
    {
        $taxonomies = Taxonomy::query()->get();
        $parentTerms = TaxonomyTerm::query()->get();

        return view('taxonomy-terms.create', compact('taxonomies', 'parentTerms'));
    }

    public function store(Request $request): RedirectResponse
    {
        // User implements: validation rules
        // User implements: TaxonomyTerm::create($validated)
        return redirect()->route('taxonomy-terms.index');
    }

    public function show(TaxonomyTerm $taxonomyTerm): View
    {
        $taxonomyTerm->load(['taxonomy', 'parent', 'children', 'posts']);

        return view('taxonomy-terms.show', compact('taxonomyTerm'));
    }

    public function edit(TaxonomyTerm $taxonomyTerm): View
    {
        $taxonomyTerm->load(['taxonomy']);
        $taxonomies = Taxonomy::query()->get();
        $parentTerms = TaxonomyTerm::query()
            ->where('id', '!=', $taxonomyTerm->id)
            ->get();

        return view('taxonomy-terms.edit', compact('taxonomyTerm', 'taxonomies', 'parentTerms'));
    }

    public function update(Request $request, TaxonomyTerm $taxonomyTerm): RedirectResponse
    {
        // User implements: validation rules
        // User implements: $taxonomyTerm->update($validated)
        return redirect()->route('taxonomy-terms.show', $taxonomyTerm);
    }

    public function destroy(TaxonomyTerm $taxonomyTerm): RedirectResponse
    {
        $taxonomyTerm->delete();

        return redirect()->route('taxonomy-terms.index');
    }
}
