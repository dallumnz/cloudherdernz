<?php

namespace App\Livewire;

use App\Models\Taxonomy;
use App\Models\TaxonomyTerm;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class TagManager extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $editingId = null;

    public string $name = '';

    public string $slug = '';

    public string $description = '';

    public string $message = '';

    public string $messageType = 'success';

    public bool $showForm = false;

    protected array $rules = [
        'name' => 'required|string|max:255',
        'slug' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
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
        $tag = TaxonomyTerm::findOrFail($id);

        // Verify it's a tag
        if ($tag->taxonomy?->type !== 'tag') {
            $this->setMessage('Invalid tag selected.', 'error');

            return;
        }

        $this->editingId = $id;
        $this->name = $tag->name;
        $this->slug = $tag->slug;
        $this->description = $tag->description ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        // Check authorization
        if ($this->editingId) {
            if (! auth()->user()?->can('edit tags')) {
                $this->setMessage('You do not have permission to edit tags.', 'error');

                return;
            }
        } else {
            if (! auth()->user()?->can('create tags')) {
                $this->setMessage('You do not have permission to create tags.', 'error');

                return;
            }
        }

        // Get or create tag taxonomy
        $tagTaxonomy = Taxonomy::query()
            ->firstOrCreate(
                ['type' => 'tag'],
                [
                    'name' => 'Tags',
                    'slug' => 'tags',
                    'description' => 'Content tags',
                    'is_hierarchical' => false,
                ]
            );

        $data = [
            'name' => $this->name,
            'slug' => $this->slug ?: \Illuminate\Support\Str::slug($this->name),
            'description' => $this->description ?: null,
            'taxonomy_id' => $tagTaxonomy->id,
            'parent_id' => null,
        ];

        if ($this->editingId) {
            $tag = TaxonomyTerm::findOrFail($this->editingId);
            $tag->update($data);
            $this->setMessage("Tag '{$tag->name}' updated successfully.", 'success');
        } else {
            $tag = TaxonomyTerm::create($data);
            $this->setMessage("Tag '{$tag->name}' created successfully.", 'success');
        }

        $this->resetForm();
        $this->showForm = false;
        $this->dispatch('tag-saved');
    }

    public function delete(int $id): void
    {
        if (! auth()->user()?->can('delete tags')) {
            $this->setMessage('You do not have permission to delete tags.', 'error');

            return;
        }

        $tag = TaxonomyTerm::findOrFail($id);

        // Verify it's a tag
        if ($tag->taxonomy?->type !== 'tag') {
            $this->setMessage('Invalid tag selected.', 'error');

            return;
        }

        $name = $tag->name;
        $tag->delete();

        $this->setMessage("Tag '{$name}' deleted successfully.", 'success');
        $this->dispatch('tag-deleted');
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->slug = '';
        $this->description = '';
        $this->resetValidation();
    }

    private function setMessage(string $message, string $type): void
    {
        $this->message = $message;
        $this->messageType = $type;
    }

    public function render(): View
    {
        $tagTaxonomy = Taxonomy::query()
            ->where('type', 'tag')
            ->first();

        $query = TaxonomyTerm::query()
            ->when($tagTaxonomy, fn ($q) => $q->where('taxonomy_id', $tagTaxonomy->id))
            ->with('taxonomy');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('slug', 'like', "%{$this->search}%");
            });
        }

        /** @var LengthAwarePaginator $tags */
        $tags = $query->latest()->paginate(10);

        return view('livewire.tag-manager', [
            'tags' => $tags,
        ]);
    }
}
