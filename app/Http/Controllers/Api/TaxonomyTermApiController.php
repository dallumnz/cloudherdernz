<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaxonomyTermResource;
use App\Models\Taxonomy;
use App\Models\TaxonomyTerm;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaxonomyTermApiController extends Controller
{
    /**
     * List all taxonomy terms with optional filtering.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = TaxonomyTerm::query()
            ->with(['taxonomy', 'parent']);

        // Filter by taxonomy
        if ($request->has('taxonomy')) {
            $query->whereHas('taxonomy', function ($q) use ($request): void {
                $q->where('slug', $request->input('taxonomy'));
            });
        }

        // Filter by taxonomy type (tags vs categories)
        if ($request->has('type')) {
            $query->whereHas('taxonomy', function ($q) use ($request): void {
                $q->where('type', $request->input('type'));
            });
        }

        // Filter by parent (for hierarchical taxonomies)
        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->input('parent_id'));
        }

        // Search by name or slug
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'name');
        $sortOrder = $request->input('sort_order', 'asc');
        $allowedSortFields = ['name', 'slug', 'created_at', 'updated_at'];

        if (in_array($sortBy, $allowedSortFields, true)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        }

        // Pagination
        $perPage = $request->input('per_page', 50);
        $perPage = min((int) $perPage, 100);

        return TaxonomyTermResource::collection($query->paginate($perPage));
    }

    /**
     * Show a single taxonomy term.
     */
    public function show(TaxonomyTerm $taxonomyTerm): TaxonomyTermResource
    {
        $taxonomyTerm->load(['taxonomy', 'parent', 'children']);

        return new TaxonomyTermResource($taxonomyTerm);
    }

    /**
     * Get posts for a specific taxonomy term.
     */
    public function posts(Request $request, TaxonomyTerm $taxonomyTerm): AnonymousResourceCollection
    {
        $posts = $taxonomyTerm->posts()
            ->with(['author', 'postable'])
            ->published()
            ->orderBy('published_at', 'desc')
            ->paginate($request->input('per_page', 15));

        return \App\Http\Resources\PostResource::collection($posts);
    }
}
