<?php

namespace App\Livewire;

use App\Models\Post;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class FeaturedImageUploader extends Component
{
    use WithFileUploads;

    public ?Post $post = null;

    #[Validate(['required', 'image', 'max:10240'])]
    public ?TemporaryUploadedFile $image = null;

    public string $collection = 'featured';

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

    public function updatedImage(): void
    {
        $this->validate([
            'image' => ['required', 'image', 'max:10240'],
        ]);

        $this->isUploading = true;
    }

    public function save(): void
    {
        $this->validate([
            'image' => ['required', 'image', 'max:10240'],
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
            // Clear existing featured image if single file collection
            $this->post->clearMediaCollection($this->collection);

            // Add new image
            $this->post->addMedia($this->image->getRealPath())
                ->usingName($this->image->getClientOriginalName())
                ->toMediaCollection($this->collection);

            $this->image = null;
            $this->isUploading = false;
            $this->setMessage('Featured image uploaded successfully.', 'success');

            // Refresh the post to get updated media
            $this->post->refresh();
        } catch (\Exception $e) {
            $this->setMessage('Failed to upload image: '.$e->getMessage(), 'error');
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

    private function setMessage(string $message, string $type): void
    {
        $this->message = $message;
        $this->messageType = $type;
    }

    public function render(): View
    {
        $featuredImage = $this->post?->getFirstMedia($this->collection);

        return view('livewire.featured-image-uploader', [
            'featuredImage' => $featuredImage,
        ]);
    }
}
