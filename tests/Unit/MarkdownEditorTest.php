<?php

use App\Livewire\MarkdownEditor;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->seed(RolePermissionSeeder::class);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('Admin');

    $this->author = User::factory()->create();
    $this->author->assignRole('Author');

    $this->viewer = User::factory()->create();
    $this->viewer->assignRole('Viewer');
});

describe('MarkdownEditor Component', function (): void {
    it('renders successfully', function (): void {
        Livewire::test(MarkdownEditor::class)
            ->assertStatus(200);
    });

    it('loads post content when postId is provided', function (): void {
        $post = Post::factory()->create([
            'content' => '# Hello World\n\nThis is test content.',
            'title' => 'Test Post',
            'status' => 'draft',
        ]);

        Livewire::test(MarkdownEditor::class, ['postId' => $post->id])
            ->assertSet('content', '# Hello World\n\nThis is test content.')
            ->assertSet('title', 'Test Post')
            ->assertSet('status', 'draft');
    });

    it('updates preview when content changes', function (): void {
        $component = Livewire::test(MarkdownEditor::class)
            ->set('content', '# Heading\n\nParagraph text');

        $component->assertSet('previewHtml', fn ($html) => str_contains($html, '<h1>'));
        $component->assertSet('previewHtml', fn ($html) => str_contains($html, '<p>'));
    });

    it('auto-saves content for authorized users', function (): void {
        $post = Post::factory()->create([
            'content' => 'Initial content',
            'author_id' => $this->author->id,
        ]);

        Livewire::actingAs($this->author)
            ->test(MarkdownEditor::class, ['postId' => $post->id])
            ->set('content', 'Updated content')
            ->call('autoSave')
            ->assertSet('isSaving', false);

        expect($post->fresh()->content)->toBe('Updated content');
    });

    it('prevents auto-save for unauthorized users', function (): void {
        $post = Post::factory()->create([
            'content' => 'Initial content',
        ]);

        Livewire::actingAs($this->viewer)
            ->test(MarkdownEditor::class, ['postId' => $post->id])
            ->set('content', 'Updated content')
            ->call('autoSave')
            ->assertSet('messageType', 'error');

        expect($post->fresh()->content)->toBe('Initial content');
    });

    it('saves content successfully', function (): void {
        $post = Post::factory()->create([
            'content' => 'Initial content',
            'author_id' => $this->author->id,
        ]);

        Livewire::actingAs($this->author)
            ->test(MarkdownEditor::class, ['postId' => $post->id])
            ->set('content', 'New saved content')
            ->call('save')
            ->assertSet('messageType', 'success');

        expect($post->fresh()->content)->toBe('New saved content');
    });

    it('validates content max length', function (): void {
        $post = Post::factory()->create([
            'author_id' => $this->author->id,
        ]);

        $longContent = str_repeat('a', 50001);

        Livewire::actingAs($this->author)
            ->test(MarkdownEditor::class, ['postId' => $post->id])
            ->set('content', $longContent)
            ->call('save')
            ->assertHasErrors(['content']);
    });

    it('toggles fullscreen mode', function (): void {
        Livewire::test(MarkdownEditor::class)
            ->assertSet('isFullscreen', false)
            ->call('toggleFullscreen')
            ->assertSet('isFullscreen', true)
            ->call('toggleFullscreen')
            ->assertSet('isFullscreen', false);
    });

    it('handles image upload insertion', function (): void {
        Livewire::test(MarkdownEditor::class)
            ->set('content', 'Initial')
            ->call('handleImageUpload', 'https://example.com/image.jpg', 'Alt text')
            ->assertSet('content', fn ($content) => str_contains($content, '![Alt text](https://example.com/image.jpg)'));
    });

    it('inserts gallery markdown correctly', function (): void {
        $imageUrls = [
            'https://example.com/img1.jpg',
            'https://example.com/img2.jpg',
        ];

        Livewire::test(MarkdownEditor::class)
            ->set('content', '')
            ->call('insertGallery', $imageUrls)
            ->assertSet('content', fn ($content) => str_contains($content, '<div class="gallery">'))
            ->assertSet('content', fn ($content) => str_contains($content, 'img1.jpg'))
            ->assertSet('content', fn ($content) => str_contains($content, 'img2.jpg'));
    });

    it('calculates word count correctly', function (): void {
        $component = Livewire::test(MarkdownEditor::class)
            ->set('content', 'This is a test with five words.');

        expect($component->get('wordCount'))->toBe(5);
    });

    it('calculates character count correctly', function (): void {
        $content = 'Hello, World!';

        $component = Livewire::test(MarkdownEditor::class)
            ->set('content', $content);

        expect($component->get('characterCount'))->toBe(strlen($content));
    });

    it('dispatches events on save', function (): void {
        $post = Post::factory()->create([
            'author_id' => $this->author->id,
        ]);

        Livewire::actingAs($this->author)
            ->test(MarkdownEditor::class, ['postId' => $post->id])
            ->set('content', 'Test content')
            ->call('save')
            ->assertDispatched('markdown-saved');
    });

    it('handles empty content gracefully', function (): void {
        Livewire::test(MarkdownEditor::class)
            ->set('content', '')
            ->assertSet('previewHtml', '');
    });

    it('shows error when saving without postId', function (): void {
        Livewire::actingAs($this->author)
            ->test(MarkdownEditor::class)
            ->set('content', 'Some content')
            ->call('save')
            ->assertSet('messageType', 'error');
    });
});
