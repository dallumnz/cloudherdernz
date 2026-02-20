<?php

namespace App\Http\Controllers\Api;

use App\Events\CommentCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    /**
     * List comments for a commentable entity.
     *
     * Query params:
     *  - per_page: Number of items per page (1-100)
     *  - include_unapproved: Include unapproved comments (requires permission)
     */
    public function index(Request $request, string $commentableType, int $commentableId): AnonymousResourceCollection|JsonResponse
    {
        // Resolve the commentable model
        $commentable = $this->resolveCommentable($commentableType, $commentableId);

        if (! $commentable) {
            return response()->json([
                'message' => 'Commentable entity not found.',
            ], 404);
        }

        $query = $commentable->comments()
            ->with(['user', 'children.user']);

        // Show only approved comments by default
        $includeUnapproved = $request->boolean('include_unapproved', false);
        if (! $includeUnapproved || ! $request->user()?->can('moderate comments')) {
            $query->approved();
        }

        // Only top-level comments (replies loaded via children)
        $query->whereNull('parent_id');

        // Order by newest first
        $query->orderBy('created_at', 'desc');

        // Pagination: Default 15, max 100
        $perPage = $request->input('per_page', 15);
        $perPage = min((int) $perPage, 100);

        return CommentResource::collection($query->paginate($perPage));
    }

    /**
     * Store a newly created comment.
     *
     * Requires authentication.
     */
    public function store(StoreCommentRequest $request, string $commentableType, int $commentableId): CommentResource|JsonResponse
    {
        Gate::authorize('create', Comment::class);

        // Resolve the commentable model
        $commentable = $this->resolveCommentable($commentableType, $commentableId);

        if (! $commentable) {
            return response()->json([
                'message' => 'Commentable entity not found.',
            ], 404);
        }

        $validated = $request->validated();

        // Create the comment
        $comment = new Comment([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
            'is_approved' => $request->user()->hasRole('Admin') || $request->user()->can('moderate comments') ? true : false,
        ]);

        // Set parent if this is a reply
        if (! empty($validated['parent_id'])) {
            $parentComment = Comment::find($validated['parent_id']);

            if (! $parentComment || $parentComment->commentable_type !== get_class($commentable) || $parentComment->commentable_id !== $commentable->id) {
                return response()->json([
                    'message' => 'Invalid parent comment.',
                ], 422);
            }

            $comment->parent_id = $validated['parent_id'];
        }

        $commentable->comments()->save($comment);

        $comment->load(['user', 'parent']);

        // Fire event if comment is approved
        if ($comment->is_approved) {
            event(new CommentCreated($comment));
        }

        return (new CommentResource($comment))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified comment.
     */
    public function show(Request $request, Comment $comment): CommentResource|JsonResponse
    {
        Gate::authorize('view', $comment);

        $comment->load(['user', 'children.user', 'commentable']);

        return new CommentResource($comment);
    }

    /**
     * Update the specified comment.
     *
     * Users can only update their own comments. Admins can update any.
     */
    public function update(UpdateCommentRequest $request, Comment $comment): CommentResource|JsonResponse
    {
        Gate::authorize('update', $comment);

        $validated = $request->validated();

        $wasApproved = $comment->is_approved;

        $comment->update([
            'body' => $validated['body'],
        ]);

        // If an admin is approving the comment, update is_approved
        if (isset($validated['is_approved']) && $request->user()->can('moderate comments')) {
            $comment->is_approved = $validated['is_approved'];
            $comment->save();
        }

        // Fire event if comment was just approved
        if (! $wasApproved && $comment->is_approved) {
            event(new CommentCreated($comment));
        }

        $comment->load(['user', 'children.user']);

        return new CommentResource($comment);
    }

    /**
     * Remove the specified comment.
     *
     * Users can only delete their own comments. Admins can delete any.
     */
    public function destroy(Request $request, Comment $comment): JsonResponse
    {
        Gate::authorize('delete', $comment);

        // Soft delete the comment
        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully.',
        ], 200);
    }

    /**
     * Resolve the commentable model from type and id.
     */
    private function resolveCommentable(string $type, int $id): ?Model
    {
        $modelClass = match ($type) {
            'posts' => Post::class,
            default => null,
        };

        if (! $modelClass) {
            return null;
        }

        try {
            return $modelClass::query()->find($id);
        } catch (ModelNotFoundException) {
            return null;
        }
    }
}
