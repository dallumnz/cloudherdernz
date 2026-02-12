<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('edit categories') ?? false;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('taxonomy_terms', 'slug')
                    ->where(fn ($query) => $query->whereHas('taxonomy', fn ($q) => $q->where('type', 'category')))
                    ->ignore($categoryId),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:taxonomy_terms,id',
                function ($attribute, $value, $fail) use ($categoryId) {
                    if ($value && $value == $categoryId) {
                        $fail('A category cannot be its own parent.');
                    }
                },
            ],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The category name is required.',
            'name.max' => 'The category name cannot exceed 255 characters.',
            'slug.required' => 'The category slug is required.',
            'slug.unique' => 'This slug is already in use for another category.',
            'description.max' => 'The description cannot exceed 1000 characters.',
            'parent_id.exists' => 'The selected parent category does not exist.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('name') && ! $this->filled('slug')) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->input('name')),
            ]);
        }
    }
}
