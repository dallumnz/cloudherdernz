<?php

namespace App\Http\Controllers;

use App\Models\Taxonomy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaxonomyController extends Controller
{
    public function index(): View
    {
        $taxonomies = Taxonomy::query()->latest()->paginate(20);

        return view('taxonomies.index', compact('taxonomies'));
    }

    public function create(): View
    {
        return view('taxonomies.create');
    }

    public function store(Request $request): RedirectResponse
    {
        // User implements: validation rules
        // User implements: Taxonomy::create($validated)
        return redirect()->route('taxonomies.index');
    }

    public function show(Taxonomy $taxonomy): View
    {
        $taxonomy->load(['terms']);

        return view('taxonomies.show', compact('taxonomy'));
    }

    public function edit(Taxonomy $taxonomy): View
    {
        return view('taxonomies.edit', compact('taxonomy'));
    }

    public function update(Request $request, Taxonomy $taxonomy): RedirectResponse
    {
        // User implements: validation rules
        // User implements: $taxonomy->update($validated)
        return redirect()->route('taxonomies.show', $taxonomy);
    }

    public function destroy(Taxonomy $taxonomy): RedirectResponse
    {
        $taxonomy->delete();

        return redirect()->route('taxonomies.index');
    }
}
