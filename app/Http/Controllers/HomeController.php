<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\TaxonomyTerm;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featuredPosts = Post::query()
            ->published()
            ->excludeNewsletters()
            ->with(['postable', 'author', 'taxonomyTerms', 'media'])
            ->latest('published_at')
            ->take(6)
            ->get();

        $recentPosts = Post::query()
            ->published()
            ->excludeNewsletters()
            ->with(['postable', 'author', 'media'])
            ->latest('published_at')
            ->take(10)
            ->get();

        $popularTags = TaxonomyTerm::query()
            ->whereHas('taxonomy', fn ($q) => $q->where('type', 'tag'))
            ->withCount('posts')
            ->orderByDesc('posts_count')
            ->take(10)
            ->get();

        $categories = TaxonomyTerm::query()
            ->whereHas('taxonomy', fn ($q) => $q->where('type', 'category'))
            ->withCount('posts')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('home', [
            'featuredPosts' => $featuredPosts,
            'recentPosts' => $recentPosts,
            'popularTags' => $popularTags,
            'categories' => $categories,
        ]);
    }
}
