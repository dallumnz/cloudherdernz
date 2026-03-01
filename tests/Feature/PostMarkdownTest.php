<?php

use App\Models\Post;
use App\Models\StandardPost;
use App\Models\User;
use App\Services\MarkdownService;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->user = User::factory()->create();
    $this->user->assignRole('Editor');
});

describe('Post Markdown Feature', function () {
    it('renders markdown to HTML via rendered_html accessor', function () {
        $post = Post::factory()->make([
            'markdown' => "# Title\n\nParagraph with **bold** text.",
        ]);

        $html = $post->rendered_html;

        expect($html)->toContain('<h1>Title</h1>');
        expect($html)->toContain('<strong>bold</strong>');
    });

    it('falls back to content when markdown is null', function () {
        $post = Post::factory()->make([
            'markdown' => null,
            'content' => '<p>Legacy HTML content</p>',
        ]);

        $html = $post->rendered_html;

        expect($html)->toBe('<p>Legacy HTML content</p>');
    });

    it('returns empty string when both markdown and content are null', function () {
        $post = Post::factory()->make([
            'markdown' => null,
            'content' => null,
        ]);

        $html = $post->rendered_html;

        expect($html)->toBe('');
    });

    it('hasMarkdown returns true when markdown is present', function () {
        $post = Post::factory()->make([
            'markdown' => '# Some markdown',
        ]);

        expect($post->hasMarkdown())->toBeTrue();
    });

    it('hasMarkdown returns false when markdown is null', function () {
        $post = Post::factory()->make([
            'markdown' => null,
        ]);

        expect($post->hasMarkdown())->toBeFalse();
    });

    it('displays rendered markdown on post show page', function () {
        $standardPost = StandardPost::create();
        $post = Post::factory()->published()->create([
            'author_id' => $this->user->id,
            'postable_type' => StandardPost::class,
            'postable_id' => $standardPost->id,
            'markdown' => "# Show Page Test\n\nThis is visible.",
            'content' => null,
        ]);

        $response = $this->get(route('posts.show', $post));

        $response->assertStatus(200);
        $response->assertSee('Show Page Test', false);
        $response->assertSee('This is visible.', false);
    });

    it('displays rendered markdown on post index page excerpts', function () {
        $standardPost = StandardPost::create();
        Post::factory()->published()->count(3)->create([
            'author_id' => $this->user->id,
            'postable_type' => StandardPost::class,
            'postable_id' => $standardPost->id,
            'excerpt' => null, // Force using rendered_html
            'markdown' => "# Post Title\n\nFirst paragraph with visible content.",
            'content' => null,
        ]);

        $response = $this->get(route('posts.index'));

        $response->assertStatus(200);
        // The excerpt shows the text after stripping HTML tags
        $response->assertSee('First paragraph with visible content');
    });

    it('can persist markdown to database', function () {
        $standardPost = StandardPost::create();
        $post = Post::factory()->create([
            'author_id' => $this->user->id,
            'postable_type' => StandardPost::class,
            'postable_id' => $standardPost->id,
            'markdown' => "# Persisted Markdown\n\nThis content is saved.",
            'content' => null,
        ]);

        // Fetch fresh from database
        $freshPost = Post::find($post->id);

        expect($freshPost->markdown)->toBe("# Persisted Markdown\n\nThis content is saved.");
        expect($freshPost->hasMarkdown())->toBeTrue();
        expect($freshPost->rendered_html)->toContain('<h1>Persisted Markdown</h1>');
    });

    it('can update markdown content', function () {
        $standardPost = StandardPost::create();
        $post = Post::factory()->create([
            'author_id' => $this->user->id,
            'postable_type' => StandardPost::class,
            'postable_id' => $standardPost->id,
            'markdown' => '# Original Content',
        ]);

        $post->update(['markdown' => "# Updated Content\n\nNew paragraph."]);
        $post->refresh();

        expect($post->markdown)->toBe("# Updated Content\n\nNew paragraph.");
        expect($post->rendered_html)->toContain('<h1>Updated Content</h1>');
    });

    it('allows null markdown value in database', function () {
        $standardPost = StandardPost::create();
        $post = Post::factory()->create([
            'author_id' => $this->user->id,
            'postable_type' => StandardPost::class,
            'postable_id' => $standardPost->id,
            'markdown' => null,
            'content' => '<p>HTML content</p>',
        ]);

        $freshPost = Post::find($post->id);

        expect($freshPost->markdown)->toBeNull();
        expect($freshPost->content)->toBe('<p>HTML content</p>');
        expect($freshPost->rendered_html)->toBe('<p>HTML content</p>');
    });
});

describe('MarkdownService Integration', function () {
    it('uses MarkdownService to convert markdown', function () {
        $service = app(MarkdownService::class);

        $html = $service->toHtml('# Test');

        expect($html)->toContain('<h1>Test</h1>');
    });

    it('escapes unsafe HTML from markdown input', function () {
        $post = Post::factory()->make([
            'markdown' => "# Safe Content\n\n<script>alert('xss')</script>",
        ]);

        $html = $post->rendered_html;

        expect($html)->not->toContain('<script>');
        expect($html)->toContain('<h1>Safe Content</h1>');
        expect($html)->toContain('&lt;script&gt;');
    });
});
