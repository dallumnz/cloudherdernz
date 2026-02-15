<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Display search form or results.
     */
    public function index(Request $request): View
    {
        $query = $request->input('q', '');
        
        // If no query, show empty search form
        if (strlen(trim($query)) < 2) {
            return view('search.index', [
                'query' => '',
                'posts' => null,
            ]);
        }

        $query = trim($query);

        $posts = Post::search($query)
            ->query(fn ($q) => $q->published()->with(['author', 'taxonomyTerms', 'postable']))
            ->paginate(12);

        return view('search.index', [
            'query' => $query,
            'posts' => $posts,
        ]);
    }

    /**
     * Display dedicated search results page.
     */
    public function results(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:255', 'not_regex:/^\s+$/'],
        ]);

        $query = trim($validated['q']);

        $posts = Post::search($query)
            ->query(fn ($q) => $q->published()->with(['author', 'taxonomyTerms', 'postable']))
            ->paginate(12);

        return view('search.results', [
            'query' => $query,
            'posts' => $posts,
        ]);
    }
}
