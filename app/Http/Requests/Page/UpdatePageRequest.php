<?php

namespace App\Http\Requests\Page;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Update Page Request
 *
 * Validates data for updating an existing page.
 */
class UpdatePageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('edit pages') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $pageId = $this->route('page')?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('pages', 'slug')->ignore($pageId)],
            'content' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
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
            'title.required' => 'Please provide a title for the page.',
            'title.max' => 'The title is too long. Please keep it under 255 characters.',
            'slug.required' => 'A URL slug is required for the page.',
            'slug.unique' => 'This URL slug is already in use. Please choose a different one.',
            'slug.max' => 'The URL slug cannot exceed 255 characters.',
            'meta_title.max' => 'The meta title cannot exceed 255 characters.',
            'meta_description.max' => 'The meta description cannot exceed 500 characters.',
            'status.required' => 'Please select a status for the page.',
            'status.in' => 'The selected status is invalid. Choose from: draft or published.',
            'published_at.date' => 'Please provide a valid date and time for publication.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('title') && ! $this->has('slug')) {
            $this->merge([
                'slug' => Str::slug($this->input('title')),
            ]);
        }
    }
}
