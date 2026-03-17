<?php

namespace App\Livewire;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaUploader extends Component
{
    use WithFileUploads;
    use WithPagination;

    #[Validate(['required', 'array', 'max:20'])]
    public array $files = [];

    #[Validate(['nullable', 'string', 'max:255'])]
    public string $collectionName = 'default';

    public bool $isUploading = false;

    public ?string $message = null;

    public string $messageType = 'success';

    public string $search = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $perPage = 12;

    // Credit editing
    public ?int $editingMediaId = null;

    #[Validate(['nullable', 'string', 'max:255'])]
    public string $creditName = '';

    #[Validate(['nullable', 'string', 'max:500'])]
    public string $creditUrl = '';

    #[Validate(['nullable', 'string', 'max:1000'])]
    public string $altText = '';

    public function updatedFiles(): void
    {
        $this->validate([
            'files' => ['required', 'array', 'max:20'],
            'files.*' => ['file', 'max:10240'],
        ]);

        $this->isUploading = true;
    }

    public function save(): void
    {
        $this->validate([
            'files' => ['required', 'array', 'max:20'],
            'files.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,webp,gif,svg,pdf,doc,docx,mp3,mp4,webm'],
        ]);

        // Check authorization
        if (! auth()->user()?->can('upload media')) {
            $this->setMessage('You do not have permission to upload media.', 'error');

            return;
        }

        try {
            $uploadedCount = 0;

            foreach ($this->files as $file) {
                if ($file instanceof TemporaryUploadedFile) {
                    // Store to default disk without model association
                    $path = $file->store('media', config('media-library.disk_name', 'public'));

                    if ($path) {
                        $uploadedCount++;
                    }
                }
            }

            $this->files = [];
            $this->isUploading = false;
            $this->setMessage("{$uploadedCount} files uploaded successfully.", 'success');
        } catch (\Exception $e) {
            $this->setMessage('Failed to upload files: '.$e->getMessage(), 'error');
            $this->isUploading = false;
        }
    }

    public function editMedia(int $mediaId): void
    {
        if (! auth()->user()?->can('edit media')) {
            $this->setMessage('You do not have permission to edit media.', 'error');

            return;
        }

        $media = Media::find($mediaId);

        if (! $media) {
            $this->setMessage('Media not found.', 'error');

            return;
        }

        $this->editingMediaId = $mediaId;
        $this->creditName = $media->getCustomProperty('credit_name', '');
        $this->creditUrl = $media->getCustomProperty('credit_url', '');
        $this->altText = $media->getCustomProperty('alt_text', '');
    }

    public function saveCredit(): void
    {
        if (! auth()->user()?->can('edit media')) {
            $this->setMessage('You do not have permission to edit media.', 'error');

            return;
        }

        $this->validate([
            'creditName' => ['nullable', 'string', 'max:255'],
            'creditUrl' => ['nullable', 'string', 'max:500', 'url'],
            'altText' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $media = Media::find($this->editingMediaId);

            if (! $media) {
                $this->setMessage('Media not found.', 'error');

                return;
            }

            $media->setCustomProperty('credit_name', $this->creditName ?: null);
            $media->setCustomProperty('credit_url', $this->creditUrl ?: null);
            $media->setCustomProperty('alt_text', $this->altText ?: null);
            $media->save();

            $this->closeCreditModal();
            $this->setMessage('Credit information saved successfully.', 'success');
        } catch (\Exception $e) {
            $this->setMessage('Failed to save credit: '.$e->getMessage(), 'error');
        }
    }

    public function closeCreditModal(): void
    {
        $this->editingMediaId = null;
        $this->creditName = '';
        $this->creditUrl = '';
        $this->altText = '';
        $this->resetValidation();
    }

    public function deleteMedia(int $mediaId): void
    {
        if (! auth()->user()?->can('delete media')) {
            $this->setMessage('You do not have permission to delete media.', 'error');

            return;
        }

        try {
            $media = Media::find($mediaId);

            if ($media) {
                $media->delete();
                $this->setMessage('Media deleted successfully.', 'success');
            }
        } catch (\Exception $e) {
            $this->setMessage('Failed to delete media: '.$e->getMessage(), 'error');
        }
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    private function setMessage(string $message, string $type): void
    {
        $this->message = $message;
        $this->messageType = $type;
    }

    public function render(): View
    {
        $query = Media::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('file_name', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection);

        /** @var LengthAwarePaginator $media */
        $media = $query->paginate($this->perPage);

        return view('livewire.media-uploader', [
            'media' => $media,
        ]);
    }
}
