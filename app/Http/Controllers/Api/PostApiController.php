<?php

namespace App\Http\Controllers\Api;

use App\Enums\PostType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\AudioPost;
use App\Models\ImagePost;
use App\Models\Post;
use App\Models\VideoPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class PostApiController extends Controller
{
    /**
     * List all posts with optional filtering.
     *
     * Pagination: Default 15 items per page, max 100.
     * Query params:
     *  - per_page: Number of items per page (1-100)
     *  - page: Page number
     *  - status: Filter by status (published, draft, archived, all - requires auth)
     *  - term: Filter by taxonomy term slug
     *  - taxonomy: Filter by taxonomy slug
     *  - search: Search in title, excerpt, content
     *  - sort_by: Sort field (created_at, updated_at, published_at, title)
     *  - sort_order: Sort direction (asc, desc)
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Post::query()
            ->with(['author', 'postable', 'taxonomyTerms.taxonomy', 'media']);

        // Filter by status (default to published for public access)
        $status = $request->input('status', 'published');
        if ($request->user()?->can('view posts')) {
            $status = $request->input('status', 'published');
        }

        if ($status === 'published') {
            $query->published();
        } elseif ($status !== 'all' && $request->user()?->can('view posts')) {
            $query->where('status', $status);
        }

        // Filter by taxonomy term
        if ($request->has('term')) {
            $query->whereHas('taxonomyTerms', function ($q) use ($request): void {
                $q->where('slug', $request->input('term'));
            });
        }

        // Filter by taxonomy
        if ($request->has('taxonomy')) {
            $query->whereHas('taxonomyTerms', function ($q) use ($request): void {
                $q->whereHas('taxonomy', function ($tq) use ($request): void {
                    $tq->where('slug', $request->input('taxonomy'));
                });
            });
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'published_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $allowedSortFields = ['created_at', 'updated_at', 'published_at', 'title'];

        if (in_array($sortBy, $allowedSortFields, true)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        // Pagination: Default 15, max 100
        $perPage = $request->input('per_page', 15);
        $perPage = min((int) $perPage, 100);

        return PostResource::collection($query->paginate($perPage));
    }

    /**
     * Store a newly created post.
     *
     * Requires authentication and 'create posts' permission.
     */
    public function store(StorePostRequest $request): PostResource|JsonResponse
    {
        $validated = $request->validated();

        return DB::transaction(function () use ($request, $validated) {
            // Create the type-specific postable entity
            $postable = $this->createPostable($validated['post_type'], $validated);

            if (! $postable) {
                return response()->json([
                    'message' => 'Invalid post type.',
                ], 422);
            }

            // Create the main post
            $post = Post::create([
                'author_id' => $request->user()->id,
                'title' => $validated['title'],
                'slug' => $validated['slug'],
                'excerpt' => $validated['excerpt'] ?? null,
                'content' => $validated['content'] ?? null,
                'status' => $validated['status'],
                'published_at' => $validated['published_at'] ?? now(),
                'postable_type' => get_class($postable),
                'postable_id' => $postable->id,
            ]);

            // Attach taxonomy terms if provided
            if (! empty($validated['taxonomy_terms'])) {
                $post->taxonomyTerms()->attach($validated['taxonomy_terms']);
            }

            $post->load(['author', 'postable', 'taxonomyTerms.taxonomy', 'media']);

            return (new PostResource($post))
                ->response()
                ->setStatusCode(201);
        });
    }

    /**
     * Show a single post.
     */
    public function show(Request $request, Post $post): PostResource|JsonResponse
    {
        // Check if user can view this post
        if ($post->status !== 'published' && ! $request->user()?->can('view posts')) {
            return response()->json([
                'message' => 'This post is not available.',
            ], 403);
        }

        $post->load(['author', 'postable', 'taxonomyTerms.taxonomy', 'media']);

        return new PostResource($post);
    }

    /**
     * Update the specified post.
     *
     * Requires authentication and 'edit posts' permission.
     */
    public function update(UpdatePostRequest $request, Post $post): PostResource|JsonResponse
    {
        $validated = $request->validated();

        // Update the main post fields
        $post->update([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'] ?? null,
            'status' => $validated['status'],
            'published_at' => $validated['published_at'] ?? $post->published_at,
        ]);

        // Update the type-specific postable entity
        $this->updatePostable($post->postable, $validated['post_type'], $validated);

        // Sync taxonomy terms if provided
        if (isset($validated['taxonomy_terms'])) {
            $post->taxonomyTerms()->sync($validated['taxonomy_terms']);
        }

        $post->load(['author', 'postable', 'taxonomyTerms.taxonomy', 'media']);

        return new PostResource($post);
    }

    /**
     * Update only the content field of a post.
     * Used for auto-save and markdown editor updates.
     *
     * Requires authentication and 'edit posts' permission.
     */
    public function updateContent(Request $request, Post $post): PostResource|JsonResponse
    {
        if (! $request->user()?->can('edit posts')) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 403);
        }

        $validated = $request->validate([
            'content' => ['nullable', 'string', 'max:50000'],
        ]);

        $post->update([
            'content' => $validated['content'] ?? null,
        ]);

        $post->load(['author', 'postable', 'taxonomyTerms.taxonomy', 'media']);

        return new PostResource($post);
    }

    /**
     * Remove the specified post.
     *
     * Requires authentication and 'delete posts' permission.
     */
    public function destroy(Request $request, Post $post): JsonResponse
    {
        if (! $request->user()?->can('delete posts')) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 403);
        }

        return DB::transaction(function () use ($post) {
            // Delete the type-specific entity first
            $post->postable?->delete();

            // Delete the post (this will also detach taxonomy terms via pivot table)
            $post->delete();

            return response()->json([
                'message' => 'Post deleted successfully.',
            ], 200);
        });
    }

    /**
     * Get posts by post type.
     *
     * Pagination: Default 15 items per page, max 100.
     */
    public function byType(Request $request, string $type): AnonymousResourceCollection|JsonResponse
    {
        // Map slug to enum
        $postType = match ($type) {
            'image' => PostType::IMAGE,
            'video' => PostType::VIDEO,
            'audio' => PostType::AUDIO,
            default => null,
        };

        if (! $postType) {
            return response()->json([
                'message' => 'Post type not found.',
            ], 404);
        }

        $query = Post::query()
            ->where('postable_type', $postType->model())
            ->with(['author', 'postable', 'taxonomyTerms.taxonomy', 'media']);

        // Filter by status
        $status = $request->input('status', 'published');
        if ($status === 'published') {
            $query->published();
        } elseif ($status !== 'all' && $request->user()?->can('view posts')) {
            $query->where('status', $status);
        }

        // Search within type
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'published_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $allowedSortFields = ['created_at', 'updated_at', 'published_at', 'title'];

        if (in_array($sortBy, $allowedSortFields, true)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        // Pagination: Default 15, max 100
        $perPage = $request->input('per_page', 15);
        $perPage = min((int) $perPage, 100);

        return PostResource::collection($query->paginate($perPage));
    }

    /**
     * Search posts across all fields.
     *
     * Pagination: Default 15 items per page.
     */
    public function search(Request $request): AnonymousResourceCollection|JsonResponse
    {
        $query = $request->input('q');

        if (! $query || strlen($query) < 2) {
            return response()->json([
                'message' => 'Search query must be at least 2 characters.',
            ], 422);
        }

        $posts = Post::query()
            ->with(['author', 'postable', 'taxonomyTerms.taxonomy', 'media'])
            ->where(function ($q) use ($query): void {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('excerpt', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%")
                    ->orWhere('slug', 'like', "%{$query}%");
            })
            ->published()
            ->orderBy('published_at', 'desc')
            ->paginate($request->input('per_page', 15));

        return PostResource::collection($posts);
    }

    /**
     * Create a type-specific postable entity.
     */
    private function createPostable(string $postType, array $data): ImagePost|VideoPost|AudioPost|null
    {
        return match ($postType) {
            PostType::IMAGE->value => ImagePost::create([
                'caption' => $data['caption'] ?? null,
                'gallery_settings' => isset($data['gallery_settings']) ? json_decode($data['gallery_settings'], true) : null,
            ]),
            PostType::VIDEO->value => VideoPost::create([
                'video_url' => $data['video_url'] ?? null,
                'thumbnail_url' => $data['thumbnail_url'] ?? null,
                'duration_seconds' => $data['duration_seconds'] ?? null,
                'provider' => $data['provider'] ?? null,
                'episode_number' => $data['episode_number'] ?? null,
            ]),
            PostType::AUDIO->value => AudioPost::create([
                'audio_url' => $data['audio_url'] ?? null,
                'duration_seconds' => $data['duration_seconds'] ?? null,
                'episode_number' => $data['episode_number'] ?? null,
            ]),
            default => null,
        };
    }

    /**
     * Update a type-specific postable entity.
     */
    private function updatePostable($postable, string $postType, array $data): void
    {
        $updateData = match ($postType) {
            PostType::IMAGE->value => [
                'caption' => $data['caption'] ?? null,
                'gallery_settings' => isset($data['gallery_settings']) ? json_decode($data['gallery_settings'], true) : null,
            ],
            PostType::VIDEO->value => [
                'video_url' => $data['video_url'] ?? null,
                'thumbnail_url' => $data['thumbnail_url'] ?? null,
                'duration_seconds' => $data['duration_seconds'] ?? null,
                'provider' => $data['provider'] ?? null,
                'episode_number' => $data['episode_number'] ?? null,
            ],
            PostType::AUDIO->value => [
                'audio_url' => $data['audio_url'] ?? null,
                'duration_seconds' => $data['duration_seconds'] ?? null,
                'episode_number' => $data['episode_number'] ?? null,
            ],
            default => [],
        };

        if (! empty($updateData)) {
            $postable->update($updateData);
        }
    }
}
