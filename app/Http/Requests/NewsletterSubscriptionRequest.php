<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewsletterSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'preferences' => ['nullable', 'array'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Your email must not exceed 255 characters.',
            'name.max' => 'Your name must not exceed 255 characters.',
        ];
    }

    /**
     * Get validated subscriber data for storage.
     *
     * @return array<string, mixed>
     */
    public function subscriberData(): array
    {
        return [
            'email' => $this->validated('email'),
            'name' => $this->validated('name'),
            'ip_address' => $this->ip(),
            'status' => 'pending',
            'preferences' => $this->validated('preferences'),
        ];
    }
}
