<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Display search results.
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:255', 'not_regex:/^\s+$/'],
        ]);

        $query = trim($validated['q']);

        $posts = Post::search($query)
            ->query(fn ($q) => $q->published()->with(['author', 'taxonomyTerms', 'postable']))
            ->paginate(12);

        return view('search.index', [
            'query' => $query,
            'posts' => $posts,
        ]);
    }
}
