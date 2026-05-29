<?php

namespace App\Livewire;

use App\Enums\PostType;
use App\Models\Post;
use App\Models\TaxonomyTerm;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class PostManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public ?string $postTypeFilter = null;

    public ?int $editId = null;

    public bool $showCreateForm = false;

    public ?int $editingId = null;

    public string $title = '';

    public string $slug = '';

    public string $excerpt = '';

    public string $content = '';

    public ?string $postTypeValue = null;

    public string $status = 'draft';

    public ?string $publishedAt = null;

    public array $selectedTags = [];

    public array $selectedCategories = [];

    public array $seoData = [];

    public string $message = '';

    public string $messageType = 'success';

    public bool $showForm = false;

    /**
     * Event listeners for child component communication.
     */
    protected $listeners = [
        'markdown-updated' => 'handleMarkdownUpdated',
    ];

    protected $queryString = ['editId' => ['except' => null], 'showCreateForm' => ['except' => false]];

    public function mount(): void
    {
        if ($this->editId) {
            $this->edit($this->editId);
        } elseif ($this->showCreateForm) {
            $this->create();
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedPostTypeFilter(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editingId = null;
    }

    public function edit(int $id): void
    {
        $post = Post::findOrFail($id);

        $this->editingId = $id;
        $this->title = $post->title;
        $this->slug = $post->slug;
        $this->excerpt = $post->excerpt ?? '';
        $this->content = $post->content ?? '';
        $this->postTypeValue = $post->postable_type;
        $this->status = $post->status;
        $this->publishedAt = $post->published_at?->format('Y-m-d\TH:i');
        $this->selectedTags = $post->taxonomyTerms()
            ->whereHas('taxonomy', fn ($q) => $q->where('type', 'tag'))
            ->pluck('taxonomy_terms.id')
            ->toArray();
        $this->selectedCategories = $post->taxonomyTerms()
            ->whereHas('taxonomy', fn ($q) => $q->where('type', 'category'))
            ->pluck('taxonomy_terms.id')
            ->toArray();
        $this->seoData = $post->seo?->toArray() ?? [];
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'postTypeValue' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
            'publishedAt' => 'nullable|date',
        ]);

        // Check authorization
        if ($this->editingId) {
            if (! auth()->user()?->can('edit posts')) {
                $this->setMessage('You do not have permission to edit posts.', 'error');

                return;
            }
        } else {
            if (! auth()->user()?->can('create posts')) {
                $this->setMessage('You do not have permission to create posts.', 'error');

                return;
            }
        }

        // Determine postable type
        $postableType = $this->postTypeValue ?? PostType::IMAGE->model();

        // Create or get postable
        $postable = match ($postableType) {
            PostType::IMAGE->model() => \App\Models\ImagePost::create([]),
            PostType::VIDEO->model() => \App\Models\VideoPost::create([
                'video_url' => 'https://example.com/video',
                'provider' => 'self',
            ]),
            PostType::AUDIO->model() => \App\Models\AudioPost::create([
                'audio_url' => 'https://example.com/audio',
            ]),
            PostType::NEWSLETTER->model() => \App\Models\NewsletterPost::create([
                'template' => 'default',
            ]),
            PostType::STANDARD->model() => \App\Models\StandardPost::create([]),
            default => \App\Models\ImagePost::create([]),
        };

        $data = [
            'title' => $this->title,
            'slug' => $this->slug ?: \Illuminate\Support\Str::slug($this->title),
            'excerpt' => $this->excerpt ?: null,
            'content' => $this->content ?: null,
            'postable_type' => $postableType,
            'postable_id' => $postable->id,
            'status' => $this->status,
            'published_at' => $this->publishedAt ?: null,
            'author_id' => auth()->id(),
        ];

        if ($this->editingId) {
            $post = Post::findOrFail($this->editingId);
            $post->update($data);
            $post->taxonomyTerms()->sync(array_merge($this->selectedTags, $this->selectedCategories));
            
            // Update SEO data
            if (!empty($this->seoData)) {
                $post->seo->update($this->seoData);
            }
            
            $this->setMessage("Post '{$post->title}' updated successfully.", 'success');
        } else {
            $post = Post::create($data);
            $post->taxonomyTerms()->attach(array_merge($this->selectedTags, $this->selectedCategories));
            
            // Create SEO data for new post
            if (!empty($this->seoData)) {
                $post->seo->update($this->seoData);
            }
            
            $this->setMessage("Post '{$post->title}' created successfully.", 'success');
        }

        $this->resetForm();
        $this->showForm = false;
        $this->dispatch('post-saved');
    }

    public function delete(int $id): void
    {
        if (! auth()->user()?->can('delete posts')) {
            $this->setMessage('You do not have permission to delete posts.', 'error');

            return;
        }

        $post = Post::findOrFail($id);
        $title = $post->title;
        $post->delete();

        $this->setMessage("Post '{$title}' deleted successfully.", 'success');
        $this->dispatch('post-deleted');
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->title = '';
        $this->slug = '';
        $this->excerpt = '';
        $this->content = '';
        $this->postTypeValue = null;
        $this->status = 'draft';
        $this->publishedAt = null;
        $this->selectedTags = [];
        $this->selectedCategories = [];
        $this->seoData = [];
        $this->resetValidation();
    }

    private function setMessage(string $message, string $type): void
    {
        $this->message = $message;
        $this->messageType = $type;
    }

    public function getTagsProperty()
    {
        return TaxonomyTerm::query()
            ->whereHas('taxonomy', fn ($q) => $q->where('type', 'tag'))
            ->orderBy('name')
            ->get();
    }

    public function getCategoriesProperty()
    {
        return TaxonomyTerm::query()
            ->whereHas('taxonomy', fn ($q) => $q->where('type', 'category'))
            ->with('parent')
            ->orderBy('name')
            ->get();
    }

    public function getPostTypesProperty()
    {
        return collect(PostType::cases())->map(function ($type) {
            return [
                'value' => $type->model(),
                'label' => $type->label(),
            ];
        });
    }

    public function render(): View
    {
        $query = Post::query()
            ->with(['postable', 'author', 'taxonomyTerms']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('slug', 'like', "%{$this->search}%")
                    ->orWhere('excerpt', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->postTypeFilter) {
            $query->where('postable_type', $this->postTypeFilter);
        }

        /** @var LengthAwarePaginator $posts */
        $posts = $query->latest()->paginate(10);

        // Get current editing post for SEO meta box
        $post = $this->editingId ? Post::find($this->editingId) : null;

        return view('livewire.post-manager', [
            'posts' => $posts,
            'post' => $post,
        ]);
    }

    /**
     * Handle content updates from MarkdownEditor component.
     */
    public function handleMarkdownUpdated(string $content): void
    {
        $this->content = $content;
    }
}
