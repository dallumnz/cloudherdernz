<?php

namespace App\Livewire;

use App\Enums\PostType;
use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.public')]
class PostTypeFilter extends Component
{
    use WithPagination;

    public ?string $typeSlug = null;

    public string $search = '';

    public function mount(?string $type = null): void
    {
        $this->typeSlug = $type;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedTypeSlug(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $postType = null;
        $postTypeModel = null;

        if ($this->typeSlug) {
            $postType = match ($this->typeSlug) {
                'image' => PostType::IMAGE,
                'video' => PostType::VIDEO,
                'audio' => PostType::AUDIO,
                default => null,
            };
        }

        $query = Post::query()
            ->published()
            ->with(['postable', 'author', 'taxonomyTerms']);

        if ($postType) {
            $query->where('postable_type', $postType->model());
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('excerpt', 'like', "%{$this->search}%")
                    ->orWhere('content', 'like', "%{$this->search}%");
            });
        }

        /** @var LengthAwarePaginator $posts */
        $posts = $query->latest('published_at')->paginate(12);

        // Get all post types for the filter sidebar with counts
        $postTypes = collect(PostType::cases())->map(function ($type) {
            $count = Post::where('postable_type', $type->model())
                ->published()
                ->count();

            return [
                'slug' => $type->value,
                'name' => $type->label(),
                'count' => $count,
            ];
        });

        return view('livewire.post-type-filter', [
            'posts' => $posts,
            'postType' => $postType,
            'postTypes' => $postTypes,
        ]);
    }
}
