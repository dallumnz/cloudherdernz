<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'is_approved' => $this->is_approved,
            'parent_id' => $this->parent_id,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'initials' => $this->user->initials(),
                ];
            }),
            'children' => $this->whenLoaded('children', function () {
                return CommentResource::collection($this->children);
            }),
            'commentable_type' => $this->commentable_type,
            'commentable_id' => $this->commentable_id,
        ];
    }
}
