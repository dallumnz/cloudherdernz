<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'featured_image' => $this->getFeaturedImageData(),
            'author' => new UserResource($this->whenLoaded('author')),
            'status' => $this->status,
            'published_at' => $this->published_at?->toIso8601String(),
            'type' => $this->when($this->post_type !== null, fn () => [
                'slug' => $this->post_type?->value,
                'name' => $this->post_type?->label(),
                'model' => $this->postable_type,
            ]),
            'taxonomy_terms' => TaxonomyTermResource::collection($this->whenLoaded('taxonomyTerms')),
            'metadata' => $this->metadata,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }

    /**
     * Get featured image data with multiple conversions.
     *
     * @return array<string, mixed>|null
     */
    private function getFeaturedImageData(): ?array
    {
        $media = $this->getFirstMedia('featured');

        if (! $media) {
            return null;
        }

        return [
            'url' => $media->getUrl(),
            'thumb' => $media->getUrl('thumbnail'),
            'preview' => $media->getUrl('preview'),
            'alt' => $media->getCustomProperty('alt', $this->title),
        ];
    }
}
