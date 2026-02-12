<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\TaxonomyTerm;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class PublicHomepage extends Component
{
    public function render(): View
    {
        $featuredPosts = Post::query()
            ->published()
            ->with(['postable', 'author', 'taxonomyTerms'])
            ->latest('published_at')
            ->take(6)
            ->get();

        $recentPosts = Post::query()
            ->published()
            ->with(['postable', 'author'])
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

        return view('livewire.public-homepage', [
            'featuredPosts' => $featuredPosts,
            'recentPosts' => $recentPosts,
            'popularTags' => $popularTags,
            'categories' => $categories,
        ]);
    }
}
