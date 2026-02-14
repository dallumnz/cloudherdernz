<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Livewire\Component;

class SearchPosts extends Component
{
    public string $query = '';

    public int $page = 1;

    public int $perPage = 12;

    public array $results = [];

    public array $pagination = [];

    public bool $isLoading = false;

    public ?string $errorMessage = null;

    public function mount(): void
    {
        $this->fetchPosts();
    }

    public function updatedQuery(): void
    {
        $this->page = 1;
        $this->fetchPosts();
    }

    public function updatedPage(): void
    {
        $this->fetchPosts();
    }

    public function updatedPerPage(): void
    {
        $this->page = 1;
        $this->fetchPosts();
    }

    public function fetchPosts(): void
    {
        $this->isLoading = true;
        $this->errorMessage = null;

        try {
            $response = Http::get(
                url('/api/v1/posts'),
                [
                    'search' => $this->query,
                    'page' => $this->page,
                    'per_page' => $this->perPage,
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                $this->results = $data['data'] ?? [];
                $this->pagination = $data['meta'] ?? [];
            } else {
                $this->errorMessage = 'Failed to load posts. Please try again.';
                $this->results = [];
                $this->pagination = [];
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'An error occurred while loading posts.';
            $this->results = [];
            $this->pagination = [];
        } finally {
            $this->isLoading = false;
        }
    }

    public function getTotalPagesProperty(): int
    {
        return $this->pagination['last_page'] ?? 1;
    }

    public function getCurrentPageProperty(): int
    {
        return $this->pagination['current_page'] ?? 1;
    }

    public function getTotalResultsProperty(): int
    {
        return $this->pagination['total'] ?? 0;
    }

    public function hasResults(): bool
    {
        return count($this->results) > 0;
    }

    public function hasMorePages(): bool
    {
        return $this->getCurrentPageProperty() < $this->getTotalPagesProperty();
    }

    public function hasPreviousPages(): bool
    {
        return $this->getCurrentPageProperty() > 1;
    }

    public function nextPage(): void
    {
        if ($this->hasMorePages()) {
            $this->page = $this->getCurrentPageProperty() + 1;
            $this->fetchPosts();
        }
    }

    public function previousPage(): void
    {
        if ($this->hasPreviousPages()) {
            $this->page = $this->getCurrentPageProperty() - 1;
            $this->fetchPosts();
        }
    }

    public function goToPage(int $page): void
    {
        if ($page >= 1 && $page <= $this->getTotalPagesProperty()) {
            $this->page = $page;
            $this->fetchPosts();
        }
    }

    public function render(): View
    {
        return view('livewire.search-posts');
    }
}
