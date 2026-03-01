<?php

namespace App\Livewire;

use App\Models\Post;
use Illuminate\View\View;
use League\CommonMark\CommonMarkConverter;
use Livewire\Component;

/**
 * Markdown Editor Component
 *
 * Provides a rich markdown editing experience with EasyMDE integration,
 * live preview, auto-save, and image upload support.
 */
class MarkdownEditor extends Component
{
    public ?int $postId = null;

    public string $content = '';

    public string $title = '';

    public string $previewHtml = '';

    public bool $isFullscreen = false;

    public bool $isSaving = false;

    public ?string $lastSavedAt = null;

    public string $status = 'draft';

    public ?string $message = null;

    public string $messageType = 'success';

    protected CommonMarkConverter $markdownConverter;

    public function boot(): void
    {
        $this->markdownConverter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    public function mount(?int $postId = null): void
    {
        if ($postId) {
            $post = Post::find($postId);
            if ($post) {
                $this->postId = $post->id;
                $this->content = $post->content ?? '';
                $this->title = $post->title;
                $this->status = $post->status;
                $this->lastSavedAt = $post->updated_at?->diffForHumans();
            }
        }

        $this->updatePreview();
    }

    /**
     * Update the live preview when content changes.
     */
    public function updatedContent(): void
    {
        $this->updatePreview();
    }

    /**
     * Update the HTML preview from markdown content.
     */
    private function updatePreview(): void
    {
        if (empty($this->content)) {
            $this->previewHtml = '';

            return;
        }

        $this->previewHtml = $this->markdownConverter->convert($this->content)->getContent();
    }

    /**
     * Auto-save the draft content to the API.
     */
    public function autoSave(): void
    {
        if (! $this->postId) {
            return;
        }

        $this->isSaving = true;

        try {
            $post = Post::findOrFail($this->postId);

            // Check authorization
            if (! auth()->user()?->can('edit posts')) {
                $this->setMessage('You do not have permission to edit this post.', 'error');
                $this->isSaving = false;

                return;
            }

            $post->update([
                'content' => $this->content,
            ]);

            $this->lastSavedAt = now()->diffForHumans();
            $this->setMessage('Draft saved automatically.', 'success');
        } catch (\Exception $e) {
            $this->setMessage('Failed to save draft: '.$e->getMessage(), 'error');
        } finally {
            $this->isSaving = false;
        }
    }

    /**
     * Save the content and update the post.
     */
    public function save(): void
    {
        if (! $this->postId) {
            $this->setMessage('No post selected.', 'error');

            return;
        }

        $this->validate([
            'content' => 'nullable|string|max:50000',
        ]);

        try {
            $post = Post::findOrFail($this->postId);

            if (! auth()->user()?->can('edit posts')) {
                $this->setMessage('You do not have permission to edit this post.', 'error');

                return;
            }

            $post->update([
                'content' => $this->content,
            ]);

            $this->lastSavedAt = now()->diffForHumans();
            $this->setMessage('Content saved successfully.', 'success');
            $this->dispatch('markdown-saved', postId: $this->postId);
        } catch (\Exception $e) {
            $this->setMessage('Failed to save: '.$e->getMessage(), 'error');
        }
    }

    /**
     * Toggle fullscreen mode.
     */
    public function toggleFullscreen(): void
    {
        $this->isFullscreen = ! $this->isFullscreen;
        $this->dispatch('toggle-fullscreen', isFullscreen: $this->isFullscreen);
    }

    /**
     * Handle image upload from the editor.
     */
    public function handleImageUpload(string $imageUrl, ?string $altText = null): void
    {
        $markdown = "![{$altText}]({$imageUrl})";
        $this->content .= "\n\n{$markdown}\n";
        $this->updatePreview();
        $this->dispatch('image-inserted', markdown: $markdown);
    }

    /**
     * Insert a gallery of images into the markdown.
     */
    public function insertGallery(array $imageUrls): void
    {
        $galleryMarkdown = "\n\n<div class=\"gallery\">\n";
        foreach ($imageUrls as $url) {
            $galleryMarkdown .= "  <img src=\"{$url}\" alt=\"Gallery image\" />\n";
        }
        $galleryMarkdown .= '</div>\n';

        $this->content .= $galleryMarkdown;
        $this->updatePreview();
        $this->dispatch('gallery-inserted', count: count($imageUrls));
    }

    /**
     * Get the rendered HTML content for display.
     */
    public function getRenderedContentProperty(): string
    {
        if (empty($this->content)) {
            return '';
        }

        return $this->markdownConverter->convert($this->content)->getContent();
    }

    /**
     * Get word count for the content.
     */
    public function getWordCountProperty(): int
    {
        if (empty($this->content)) {
            return 0;
        }

        return str_word_count(strip_tags($this->content));
    }

    /**
     * Get character count for the content.
     */
    public function getCharacterCountProperty(): int
    {
        return strlen($this->content);
    }

    private function setMessage(string $message, string $type): void
    {
        $this->message = $message;
        $this->messageType = $type;

        // Clear message after 3 seconds
        $this->dispatch('clear-message');
    }

    public function clearMessage(): void
    {
        $this->message = null;
    }

    public function render(): View
    {
        return view('livewire.markdown-editor');
    }
}
