<?php

namespace App\Livewire;

use App\Models\Page;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class PageManager extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $editingId = null;

    public string $title = '';

    public string $slug = '';

    public string $content = '';

    public string $status = 'draft';

    public string $meta_title = '';

    public string $meta_description = '';

    public string $message = '';

    public string $messageType = 'success';

    public bool $showForm = false;

    protected array $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255|alpha_dash',
        'content' => 'nullable|string',
        'status' => 'required|in:draft,published',
        'meta_title' => 'nullable|string|max:255',
        'meta_description' => 'nullable|string|max:500',
    ];

    public function updatedSearch(): void
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
        $page = Page::findOrFail($id);

        $this->editingId = $page->id;
        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->content = $page->content ?? '';
        $this->status = $page->status;
        $this->meta_title = $page->meta_title ?? '';
        $this->meta_description = $page->meta_description ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingId) {
            $page = Page::findOrFail($this->editingId);
            $page->update([
                'title' => $this->title,
                'slug' => $this->slug,
                'content' => $this->content ?: null,
                'status' => $this->status,
                'meta_title' => $this->meta_title ?: null,
                'meta_description' => $this->meta_description ?: null,
                'published_at' => $this->status === 'published' ? now() : null,
            ]);
            $this->setMessage('Page updated successfully.');
        } else {
            Page::create([
                'title' => $this->title,
                'slug' => $this->slug,
                'content' => $this->content ?: null,
                'status' => $this->status,
                'meta_title' => $this->meta_title ?: null,
                'meta_description' => $this->meta_description ?: null,
                'author_id' => auth()->id(),
                'published_at' => $this->status === 'published' ? now() : null,
            ]);
            $this->setMessage('Page created successfully.');
        }

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $page = Page::findOrFail($id);
        $page->delete();
        $this->setMessage('Page deleted successfully.');
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->title = '';
        $this->slug = '';
        $this->content = '';
        $this->status = 'draft';
        $this->meta_title = '';
        $this->meta_description = '';
        $this->editingId = null;
        $this->showForm = false;
    }

    protected function setMessage(string $message, string $type = 'success'): void
    {
        $this->message = $message;
        $this->messageType = $type;
    }

    public function render(): View
    {
        $pages = Page::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', "%{$this->search}%")
                    ->orWhere('slug', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(15);

        return view('livewire.page-manager', compact('pages'));
    }
}
