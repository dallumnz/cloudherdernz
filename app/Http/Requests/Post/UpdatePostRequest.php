<?php

namespace App\Http\Requests\Post;

use App\Enums\PostType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Update Post Request
 *
 * Validates data for updating an existing post. Handles validation
 * for all post types and allows post type changes with proper
 * validation of type-specific fields.
 */
class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('edit posts') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $postTypeValues = array_map(fn ($type) => $type->value, PostType::cases());
        $postId = $this->route('post')?->id ?? $this->input('id');

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('posts', 'slug')->ignore($postId),
            ],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'post_type' => ['required', 'string', 'in:'.implode(',', $postTypeValues)],
            'status' => ['required', 'in:draft,published,archived'],
            'published_at' => ['nullable', 'date'],
            'taxonomy_terms' => ['nullable', 'array'],
            'taxonomy_terms.*' => ['integer', 'exists:taxonomy_terms,id'],
            // Type-specific fields
            'caption' => ['nullable', 'string'],
            'gallery_settings' => ['nullable', 'json'],
            'video_url' => ['nullable', 'string'],
            'thumbnail_url' => ['nullable', 'string'],
            'duration_seconds' => ['nullable', 'integer'],
            'provider' => ['nullable', 'string', 'in:youtube,vimeo,self'],
            'episode_number' => ['nullable', 'integer'],
            'audio_url' => ['nullable', 'string'],
            // Newsletter Post Fields
            'template' => ['nullable', 'string', 'in:default,minimal,promotional'],
            'subscriber_settings' => ['nullable', 'json'],
            // SEO Fields
            'seo' => ['nullable', 'array'],
            'seo.title' => ['nullable', 'string', 'max:70'],
            'seo.description' => ['nullable', 'string', 'max:160'],
            'seo.image' => ['nullable', 'string'],
            'seo.robots' => ['nullable', 'string', 'in:index,follow,noindex,nofollow,noindex,nofollow'],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please provide a title for your post.',
            'title.max' => 'The title is too long. Please keep it under 255 characters.',
            'slug.required' => 'A URL slug is required for the post.',
            'slug.unique' => 'This URL slug is already in use by another post. Please choose a different one.',
            'slug.max' => 'The URL slug cannot exceed 255 characters.',
            'post_type.required' => 'Please select a post type (Image, Video, or Audio).',
            'post_type.in' => 'The selected post type is not valid. Please choose from the available options.',
            'status.required' => 'Please select a status for the post.',
            'status.in' => 'The selected status is invalid. Choose from: draft, published, or archived.',
            'published_at.date' => 'Please provide a valid date and time for publication.',
            'published_at.date_format' => 'The publication date format is invalid. Use YYYY-MM-DD HH:MM.',
            'taxonomy_terms.array' => 'Taxonomy terms must be provided as a list.',
            'taxonomy_terms.*.integer' => 'Invalid taxonomy term ID format.',
            'taxonomy_terms.*.exists' => 'One or more selected tags or categories do not exist in the system.',
            'caption.string' => 'The caption must be text.',
            'gallery_settings.json' => 'Gallery settings must be valid JSON format.',
            'video_url.url' => 'The video URL must be a valid web address.',
            'thumbnail_url.url' => 'The thumbnail URL must be a valid web address.',
            'duration_seconds.integer' => 'Duration must be a whole number of seconds.',
            'duration_seconds.min' => 'Duration cannot be negative.',
            'provider.in' => 'Please select a valid video provider (YouTube, Vimeo, or Self-hosted).',
            'episode_number.integer' => 'Episode number must be a whole number.',
            'episode_number.min' => 'Episode number must be at least 1.',
            'audio_url.url' => 'The audio URL must be a valid web address.',
            'template.in' => 'Please select a valid template (default, minimal, or promotional).',
            'subscriber_settings.json' => 'Subscriber settings must be valid JSON format.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('title') && ! $this->has('slug')) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->input('title')),
            ]);
        }
    }
}
