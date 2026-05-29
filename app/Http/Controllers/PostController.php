<?php

namespace App\Http\Controllers;

use App\Enums\PostType;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Models\AudioPost;
use App\Models\ImagePost;
use App\Models\NewsletterPost;
use App\Models\Post;
use App\Models\StandardPost;
use App\Models\TaxonomyTerm;
use App\Models\VideoPost;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Post Controller
 *
 * Handles CRUD operations for posts in the admin interface.
 * Supports multiple post types (image, video, audio) through
 * polymorphic relationships.
 */
class PostController extends Controller
{
    /**
     * Display a listing of posts.
     *
     * Uses eager loading for postable and author relationships
     * to prevent N+1 query issues.
     */
    public function index(): View
    {
        $posts = Post::query()
            ->published()
            ->excludeNewsletters()
            ->with(['postable', 'author', 'taxonomyTerms', 'media'])
            ->latest('published_at')
            ->paginate(12);

        $categories = TaxonomyTerm::query()
            ->whereHas('taxonomy', fn ($q) => $q->where('type', 'category'))
            ->withCount('posts')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        $popularTags = TaxonomyTerm::query()
            ->whereHas('taxonomy', fn ($q) => $q->where('type', 'tag'))
            ->withCount('posts')
            ->orderByDesc('posts_count')
            ->take(10)
            ->get();

        return view('posts.index', compact('posts', 'categories', 'popularTags'));
    }

    /**
     * Display the specified post.
     *
     * Eager loads postable, author, and taxonomy terms relationships.
     */
    public function show(Post $post): View
    {
        $popularPosts = Post::published()
            ->excludeNewsletters()
            ->with(['author', 'media'])
            ->latest('published_at')
            ->take(5)
            ->get();

        // Get related posts in same categories
        $categoryIds = $post->taxonomyTerms()
            ->whereHas('taxonomy', fn ($q) => $q->where('type', 'category'))
            ->pluck('taxonomy_terms.id');

        $relatedPosts = Post::published()
            ->excludeNewsletters()
            ->where('id', '!=', $post->id)
            ->whereHas('taxonomyTerms', fn ($q) => $q->whereIn('taxonomy_terms.id', $categoryIds))
            ->with(['author', 'media', 'taxonomyTerms'])
            ->latest('published_at')
            ->take(5)
            ->get();

        return view('posts.show', compact('post', 'popularPosts', 'relatedPosts'));
    }

    /**
     * Show the form for creating a new post.
     *
     * Requires 'create posts' permission.
     */
    public function create(): View
    {
        $this->authorize('create', Post::class);

        $postTypes = PostType::cases();
        $taxonomyTerms = TaxonomyTerm::query()->with('taxonomy')->get();

        return view('posts.create', compact('postTypes', 'taxonomyTerms'));
    }

    /**
     * Store a newly created post.
     *
     * Creates the type-specific postable record first, then the main post.
     * Attaches taxonomy terms if provided.
     *
     * Requires 'create posts' permission.
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        $this->authorize('create', Post::class);

        $validated = $request->validated();

        // Create the type-specific postable record
        $postType = PostType::from($validated['post_type']);
        $postable = $this->createPostable($postType, $validated);

        // Create the main post
        $post = Post::create([
            'author_id' => auth()->id(),
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'] ?? null,
            'status' => $validated['status'],
            'published_at' => $validated['published_at'] ?? null,
            'postable_type' => $postType->model(),
            'postable_id' => $postable->id,
        ]);

        // Handle SEO data
        if (! empty($validated['seo'])) {
            $post->seo->update($validated['seo']);
        }

        // Attach taxonomy terms
        if (! empty($validated['taxonomy_terms'])) {
            $post->taxonomyTerms()->attach($validated['taxonomy_terms']);
        }

        return redirect()->route('posts.show', $post)->with('success', 'Post created successfully.');
    }

    /**
     * Show the form for editing the specified post.
     *
     * Eager loads taxonomy terms and postable relationships.
     * Requires 'edit posts' permission.
     */
    public function edit(Post $post): View
    {
        $this->authorize('update', $post);

        $post->load(['taxonomyTerms', 'postable']);
        $postTypes = PostType::cases();
        $taxonomyTerms = TaxonomyTerm::query()->with('taxonomy')->get();

        return view('posts.edit', compact('post', 'postTypes', 'taxonomyTerms'));
    }

    /**
     * Update the specified post.
     *
     * Handles post type changes by creating new postable records when needed.
     * Syncs taxonomy terms with the post.
     *
     * Requires 'edit posts' permission.
     */
    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        $validated = $request->validated();

        // Handle postable type change or update existing
        $postType = PostType::from($validated['post_type']);
        $currentType = $post->post_type;

        if ($currentType !== $postType) {
            // Type changed - create new postable
            $postable = $this->createPostable($postType, $validated);
            $post->postable_type = $postType->model();
            $post->postable_id = $postable->id;
        } else {
            // Update existing postable
            $this->updatePostable($post->postable, $postType, $validated);
        }

        // Update the main post
        $post->update([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'] ?? null,
            'status' => $validated['status'],
            'published_at' => $validated['published_at'] ?? null,
        ]);

        // Handle SEO data
        if (! empty($validated['seo'])) {
            $post->seo->update($validated['seo']);
        }

        // Sync taxonomy terms
        $post->taxonomyTerms()->sync($validated['taxonomy_terms'] ?? []);

        return redirect()->route('posts.show', $post)->with('success', 'Post updated successfully.');
    }

    /**
     * Remove the specified post.
     *
     * Also deletes the associated postable record.
     * Requires 'delete posts' permission.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        // Delete the postable record as well
        if ($post->postable) {
            $post->postable->delete();
        }

        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully.');
    }

    /**
     * Create a type-specific postable record.
     */
    private function createPostable(PostType $type, array $data): Model
    {
        return match ($type) {
            PostType::STANDARD => StandardPost::create([]),
            PostType::IMAGE => ImagePost::create([
                'caption' => $data['caption'] ?? null,
                'gallery_settings' => isset($data['gallery_settings']) ? json_decode($data['gallery_settings'], true) : null,
            ]),
            PostType::VIDEO => VideoPost::create([
                'video_url' => $data['video_url'] ?? 'https://example.com/video',
                'thumbnail_url' => $data['thumbnail_url'] ?? null,
                'duration_seconds' => $data['duration_seconds'] ?? null,
                'provider' => $data['provider'] ?? 'self',
                'episode_number' => $data['episode_number'] ?? null,
            ]),
            PostType::AUDIO => AudioPost::create([
                'audio_url' => $data['audio_url'] ?? 'https://example.com/audio',
                'duration_seconds' => $data['duration_seconds'] ?? null,
                'episode_number' => $data['episode_number'] ?? null,
            ]),
            PostType::NEWSLETTER => NewsletterPost::create([
                'template' => $data['template'] ?? 'default',
                'subscriber_settings' => isset($data['subscriber_settings']) ? json_decode($data['subscriber_settings'], true) : null,
            ]),
        };
    }

    /**
     * Update a type-specific postable record.
     */
    private function updatePostable(Model $postable, PostType $type, array $data): void
    {
        match ($type) {
            PostType::STANDARD => $postable->update([]),
            PostType::IMAGE => $postable->update([
                'caption' => $data['caption'] ?? $postable->caption,
                'gallery_settings' => isset($data['gallery_settings']) ? json_decode($data['gallery_settings'], true) : $postable->gallery_settings,
            ]),
            PostType::VIDEO => $postable->update([
                'video_url' => $data['video_url'] ?? $postable->video_url,
                'thumbnail_url' => $data['thumbnail_url'] ?? $postable->thumbnail_url,
                'duration_seconds' => $data['duration_seconds'] ?? $postable->duration_seconds,
                'provider' => $data['provider'] ?? $postable->provider,
                'episode_number' => $data['episode_number'] ?? $postable->episode_number,
            ]),
            PostType::AUDIO => $postable->update([
                'audio_url' => $data['audio_url'] ?? $postable->audio_url,
                'duration_seconds' => $data['duration_seconds'] ?? $postable->duration_seconds,
                'episode_number' => $data['episode_number'] ?? $postable->episode_number,
            ]),
            PostType::NEWSLETTER => $postable->update([
                'template' => $data['template'] ?? $postable->template,
                'subscriber_settings' => isset($data['subscriber_settings']) ? json_decode($data['subscriber_settings'], true) : $postable->subscriber_settings,
            ]),
        };
    }
}
