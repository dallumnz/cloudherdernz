<?php

namespace App\Livewire;

use App\Models\Taxonomy;
use App\Models\TaxonomyTerm;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryManager extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $editingId = null;

    public string $name = '';

    public string $slug = '';

    public string $description = '';

    public ?int $parentId = null;

    public string $message = '';

    public string $messageType = 'success';

    public bool $showForm = false;

    protected array $rules = [
        'name' => 'required|string|max:255',
        'slug' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'parentId' => 'nullable|integer|exists:taxonomy_terms,id',
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
        $category = TaxonomyTerm::findOrFail($id);

        // Verify it's a category
        if ($category->taxonomy?->type !== 'category') {
            $this->setMessage('Invalid category selected.', 'error');

            return;
        }

        $this->editingId = $id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description ?? '';
        $this->parentId = $category->parent_id;
        $this->showForm = true;
    }

    public function save(): void
    {
        // Validate parent_id separately to prevent self-parenting
        if ($this->parentId && $this->parentId == $this->editingId) {
            $this->addError('parentId', 'A category cannot be its own parent.');

            return;
        }

        $this->validate();

        // Check authorization
        if ($this->editingId) {
            if (! auth()->user()?->can('edit categories')) {
                $this->setMessage('You do not have permission to edit categories.', 'error');

                return;
            }
        } else {
            if (! auth()->user()?->can('create categories')) {
                $this->setMessage('You do not have permission to create categories.', 'error');

                return;
            }
        }

        // Get or create category taxonomy
        $categoryTaxonomy = Taxonomy::query()
            ->firstOrCreate(
                ['type' => 'category'],
                [
                    'name' => 'Categories',
                    'slug' => 'categories',
                    'description' => 'Content categories',
                    'is_hierarchical' => true,
                ]
            );

        $data = [
            'name' => $this->name,
            'slug' => $this->slug ?: \Illuminate\Support\Str::slug($this->name),
            'description' => $this->description ?: null,
            'taxonomy_id' => $categoryTaxonomy->id,
            'parent_id' => $this->parentId ?: null,
        ];

        if ($this->editingId) {
            $category = TaxonomyTerm::findOrFail($this->editingId);
            $category->update($data);
            $this->setMessage("Category '{$category->name}' updated successfully.", 'success');
        } else {
            $category = TaxonomyTerm::create($data);
            $this->setMessage("Category '{$category->name}' created successfully.", 'success');
        }

        $this->resetForm();
        $this->showForm = false;
        $this->dispatch('category-saved');
    }

    public function delete(int $id): void
    {
        if (! auth()->user()?->can('delete categories')) {
            $this->setMessage('You do not have permission to delete categories.', 'error');

            return;
        }

        $category = TaxonomyTerm::findOrFail($id);

        // Verify it's a category
        if ($category->taxonomy?->type !== 'category') {
            $this->setMessage('Invalid category selected.', 'error');

            return;
        }

        $name = $category->name;

        // Reassign children to parent or make them root
        if ($category->children()->count() > 0) {
            $category->children()->update(['parent_id' => $category->parent_id]);
        }

        $category->delete();

        $this->setMessage("Category '{$name}' deleted successfully.", 'success');
        $this->dispatch('category-deleted');
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
        $this->parentId = null;
        $this->resetValidation();
    }

    private function setMessage(string $message, string $type): void
    {
        $this->message = $message;
        $this->messageType = $type;
    }

    /**
     * Get available parent categories for dropdown.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getParentCategoriesProperty()
    {
        $categoryTaxonomy = Taxonomy::query()
            ->where('type', 'category')
            ->first();

        return TaxonomyTerm::query()
            ->when($categoryTaxonomy, fn ($q) => $q->where('taxonomy_id', $categoryTaxonomy->id))
            ->when($this->editingId, fn ($q) => $q->where('id', '!=', $this->editingId))
            ->orderBy('name')
            ->get();
    }

    public function render(): View
    {
        $categoryTaxonomy = Taxonomy::query()
            ->where('type', 'category')
            ->first();

        $query = TaxonomyTerm::query()
            ->when($categoryTaxonomy, fn ($q) => $q->where('taxonomy_id', $categoryTaxonomy->id))
            ->with(['taxonomy', 'parent', 'children'])
            ->whereNull('parent_id'); // Only root categories

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('slug', 'like', "%{$this->search}%");
            });
        }

        /** @var LengthAwarePaginator $categories */
        $categories = $query->latest()->paginate(10);

        return view('livewire.category-manager', [
            'categories' => $categories,
        ]);
    }
}
