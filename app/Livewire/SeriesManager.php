<?php

namespace App\Livewire;

use App\Models\Series;
use App\Models\Taxonomy;
use App\Models\TaxonomyTerm;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class SeriesManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    /**
     * Event listeners for child component communication.
     */
    protected $listeners = [
        'seo-data-updated' => 'handleSeoDataUpdated',
    ];

    public string $search = '';

    public ?int $editingId = null;

    public string $title = '';

    public string $slug = '';

    public string $tagline = '';

    public string $description = '';

    public string $status = 'draft';

    public ?int $taxonomyTermId = null;

    public array $seoData = [];

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $featuredImage = null;

    public string $message = '';

    public string $messageType = 'success';

    public bool $showForm = false;

    protected array $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255|alpha_dash|unique:series,slug',
        'tagline' => 'nullable|string|max:500',
        'description' => 'nullable|string',
        'status' => 'required|in:draft,published',
        'taxonomyTermId' => 'nullable|integer|exists:taxonomy_terms,id',
        'featuredImage' => 'nullable|image|mimes:jpeg,png,webp,avif|max:4096',
    ];

    protected function rules(): array
    {
        $rules = $this->rules;
        if ($this->editingId) {
            $rules['slug'] = 'required|string|max:255|alpha_dash|unique:series,slug,'.$this->editingId;
        }

        return $rules;
    }

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
        $series = Series::findOrFail($id);

        $this->editingId = $series->id;
        $this->title = $series->title;
        $this->slug = $series->slug;
        $this->tagline = $series->tagline ?? '';
        $this->description = $series->description ?? '';
        $this->status = $series->status;
        $this->taxonomyTermId = $series->taxonomy_term_id;
        $this->seoData = $series->seo?->toArray() ?? [];
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'tagline' => $this->tagline ?: null,
            'description' => $this->description ?: null,
            'status' => $this->status,
            'taxonomy_term_id' => $this->taxonomyTermId,
            'published_at' => $this->status === 'published' ? now() : null,
        ];

        if ($this->editingId) {
            $series = Series::findOrFail($this->editingId);
            $series->update($data);
            $this->setMessage('Series updated successfully.');
        } else {
            $series = Series::create($data);
            $this->setMessage('Series created successfully.');
        }

        if ($this->featuredImage) {
            $series->clearMediaCollection('featured');
            $series->addMedia($this->featuredImage->getRealPath())
                ->usingName($this->featuredImage->getClientOriginalName())
                ->toMediaCollection('featured');
        }

        if (! empty($this->seoData)) {
            $series->seo->update($this->seoData);
        }

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $series = Series::findOrFail($id);
        $series->delete();
        $this->setMessage('Series deleted successfully.');
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->title = '';
        $this->slug = '';
        $this->tagline = '';
        $this->description = '';
        $this->status = 'draft';
        $this->taxonomyTermId = null;
        $this->seoData = [];
        $this->featuredImage = null;
        $this->editingId = null;
        $this->showForm = false;
        $this->resetValidation();
    }

    /**
     * Handle SEO data updates from SeoMetaBox component.
     */
    public function handleSeoDataUpdated(array $seoData): void
    {
        $this->seoData = $seoData;
    }

    protected function setMessage(string $message, string $type = 'success'): void
    {
        $this->message = $message;
        $this->messageType = $type;
    }

    public function getSeriesTagsProperty()
    {
        $taxonomy = Taxonomy::firstOrCreate(
            ['slug' => 'series', 'type' => 'series'],
            ['name' => 'Series', 'description' => 'Tags used to group posts into series', 'is_hierarchical' => false]
        );

        return TaxonomyTerm::where('taxonomy_id', $taxonomy->id)
            ->orderBy('name')
            ->get();
    }

    public function render(): View
    {
        $seriesList = Series::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', "%{$this->search}%")
                    ->orWhere('slug', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(15);

        $series = $this->editingId ? Series::find($this->editingId) : null;

        return view('livewire.series-manager', [
            'seriesList' => $seriesList,
            'series' => $series,
        ]);
    }
}
