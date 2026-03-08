<?php

namespace App\Livewire;

use App\Models\Post;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class GalleryManager extends Component
{
    use WithFileUploads;

    public ?Post $post = null;

    #[Validate(['required', 'array', 'max:10'])]
    public array $images = [];

    public string $collection = 'gallery';

    public bool $isUploading = false;

    public ?string $message = null;

    public string $messageType = 'success';

    public function mount(Post|int|null $post = null): void
    {
        if ($post instanceof Post) {
            $this->post = $post;
        } elseif ($post) {
            $this->post = Post::find($post);
        }
    }

    public function updatedImages(): void
    {
        $this->validate([
            'images' => ['required', 'array', 'max:10'],
            'images.*' => ['image', 'max:10240'],
        ]);

        $this->isUploading = true;
    }

    public function save(): void
    {
        $this->validate([
            'images' => ['required', 'array', 'max:10'],
            'images.*' => ['image', 'max:10240'],
        ]);

        if (! $this->post) {
            $this->setMessage('No post selected.', 'error');

            return;
        }

        // Check authorization
        if (! auth()->user()?->can('edit posts')) {
            $this->setMessage('You do not have permission to upload images.', 'error');

            return;
        }

        try {
            $uploadedCount = 0;
            foreach ($this->images as $image) {
                if ($image instanceof TemporaryUploadedFile) {
                    $this->post->addMedia($image->getRealPath())
                        ->usingName($image->getClientOriginalName())
                        ->toMediaCollection($this->collection);
                    $uploadedCount++;
                }
            }

            $this->images = [];
            $this->isUploading = false;
            $this->setMessage($uploadedCount.' images uploaded successfully.', 'success');

            // Refresh the post to get updated media
            $this->post->refresh();
        } catch (\Exception $e) {
            $this->setMessage('Failed to upload images: '.$e->getMessage(), 'error');
            $this->isUploading = false;
        }
    }

    public function removeImage(int $mediaId): void
    {
        if (! auth()->user()?->can('delete posts')) {
            $this->setMessage('You do not have permission to remove images.', 'error');

            return;
        }

        try {
            $media = $this->post->getMedia($this->collection)->firstWhere('id', $mediaId);

            if ($media) {
                $media->delete();
                $this->post->refresh();
                $this->setMessage('Image removed successfully.', 'success');
            }
        } catch (\Exception $e) {
            $this->setMessage('Failed to remove image: '.$e->getMessage(), 'error');
        }
    }

    public function reorderImages(array $orderedIds): void
    {
        if (! auth()->user()?->can('edit posts')) {
            $this->setMessage('You do not have permission to reorder images.', 'error');

            return;
        }

        try {
            foreach ($orderedIds as $index => $mediaId) {
                $media = $this->post->getMedia($this->collection)->firstWhere('id', $mediaId);
                if ($media) {
                    $media->order_column = $index + 1;
                    $media->save();
                }
            }

            $this->post->refresh();
            $this->setMessage('Images reordered successfully.', 'success');
        } catch (\Exception $e) {
            $this->setMessage('Failed to reorder images: '.$e->getMessage(), 'error');
        }
    }

    private function setMessage(string $message, string $type): void
    {
        $this->message = $message;
        $this->messageType = $type;
    }

    public function render(): View
    {
        $galleryImages = $this->post?->getMedia($this->collection)->sortBy('order_column') ?? collect();

        return view('livewire.gallery-manager', [
            'galleryImages' => $galleryImages,
        ]);
    }
}
