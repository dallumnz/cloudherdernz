<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Taxonomy;
use App\Models\TaxonomyTerm;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categoryTaxonomy = Taxonomy::query()
            ->where('type', 'category')
            ->first();

        $categories = TaxonomyTerm::query()
            ->when($categoryTaxonomy, fn ($q) => $q->where('taxonomy_id', $categoryTaxonomy->id))
            ->with(['taxonomy', 'parent', 'children'])
            ->whereNull('parent_id') // Only root categories
            ->latest()
            ->paginate(20);

        return view('categories.index', compact('categories'));
    }

    public function show(TaxonomyTerm $category): View
    {
        $category->load(['taxonomy', 'parent', 'children', 'posts']);

        return view('categories.show', compact('category'));
    }

    public function create(): View
    {
        $this->authorize('create', TaxonomyTerm::class);

        $parentCategories = $this->getParentCategories();

        return view('categories.create', compact('parentCategories'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->authorize('create', TaxonomyTerm::class);

        $validated = $request->validated();

        // Get or create the category taxonomy
        $categoryTaxonomy = Taxonomy::query()
            ->firstOrCreate(
                ['type' => 'category'],
                [
                    'name' => 'Categories',
                    'slug' => 'categories',
                    'description' => 'Content categories',
                    'is_hierarchical' => true,
                ]
            );

        $validated['taxonomy_id'] = $categoryTaxonomy->id;

        $category = TaxonomyTerm::create($validated);

        return redirect()
            ->route('categories.index')
            ->with('success', "Category '{$category->name}' created successfully.");
    }

    public function edit(TaxonomyTerm $category): View
    {
        $this->authorize('update', $category);

        $parentCategories = $this->getParentCategories($category->id);

        return view('categories.edit', compact('category', 'parentCategories'));
    }

    public function update(UpdateCategoryRequest $request, TaxonomyTerm $category): RedirectResponse
    {
        $this->authorize('update', $category);

        $validated = $request->validated();

        // Prevent setting self as parent
        if (isset($validated['parent_id']) && $validated['parent_id'] == $category->id) {
            unset($validated['parent_id']);
        }

        $category->update($validated);

        return redirect()
            ->route('categories.index')
            ->with('success', "Category '{$category->name}' updated successfully.");
    }

    public function destroy(TaxonomyTerm $category): RedirectResponse
    {
        $this->authorize('delete', $category);

        $name = $category->name;

        // Reassign children to parent or make them root
        if ($category->children()->count() > 0) {
            $category->children()->update(['parent_id' => $category->parent_id]);
        }

        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', "Category '{$name}' deleted successfully.");
    }

    /**
     * Get parent categories for dropdown, optionally excluding a specific category.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getParentCategories(?int $excludeId = null)
    {
        $categoryTaxonomy = Taxonomy::query()
            ->where('type', 'category')
            ->first();

        return TaxonomyTerm::query()
            ->when($categoryTaxonomy, fn ($q) => $q->where('taxonomy_id', $categoryTaxonomy->id))
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->get();
    }
}
