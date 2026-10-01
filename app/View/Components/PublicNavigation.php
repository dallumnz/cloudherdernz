<?php

namespace App\View\Components;

use App\Enums\PostType;
use App\Models\Page;
use App\Models\TaxonomyTerm;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PublicNavigation extends Component
{
    /**
     * Get post types for navigation.
     *
     * @return array<PostType>
     */
    public function postTypes(): array
    {
        try {
            return PostType::cases();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get popular tags for navigation.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function popularTags()
    {
        return TaxonomyTerm::query()
            ->whereHas('taxonomy', fn ($q) => $q->where('type', 'tag'))
            ->withCount('posts')
            ->orderByDesc('posts_count')
            ->take(10)
            ->get();
    }

    /**
     * Get published pages that should appear in the footer menu.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function menuPages()
    {
        return Page::forMenu()->orderBy('title')->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.public-navigation', [
            'postTypes' => $this->postTypes(),
            'popularTags' => $this->popularTags(),
            'menuPages' => $this->menuPages(),
        ]);
    }
}
