<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaxonomyResource;
use App\Models\Taxonomy;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaxonomyApiController extends Controller
{
    /**
     * List all taxonomies.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Taxonomy::query()
            ->withCount('terms');

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
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

        if (in_array($sortBy, ['name', 'slug', 'created_at'], true)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $perPage = min((int) $perPage, 100);

        return TaxonomyResource::collection($query->paginate($perPage));
    }

    /**
     * Show a single taxonomy with its terms.
     */
    public function show(Taxonomy $taxonomy): TaxonomyResource
    {
        $taxonomy->load(['terms' => function ($query): void {
            $query->whereNull('parent_id')->with('children');
        }]);

        return new TaxonomyResource($taxonomy);
    }
}
