<?php

namespace App\Http\Requests;

use App\Models\ContactBlocklist;
use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'h-captcha-response' => $this->getCaptchaRules(),
        ];
    }

    /**
     * Get captcha validation rules based on environment.
     *
     * @return array<string>
     */
    protected function getCaptchaRules(): array
    {
        // Skip captcha validation in testing environment
        if (app()->environment('testing')) {
            return ['nullable', 'string'];
        }

        return ['required', 'hcaptcha'];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your name.',
            'name.max' => 'Your name must not exceed 255 characters.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Your email must not exceed 255 characters.',
            'subject.max' => 'The subject must not exceed 255 characters.',
            'message.required' => 'Please enter your message.',
            'message.max' => 'Your message must not exceed 5000 characters.',
            'h-captcha-response.required' => 'Please complete the CAPTCHA verification.',
            'h-captcha-response.hcaptcha' => 'CAPTCHA verification failed. Please try again.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Check if email is blocked
        if ($this->has('email') && ContactBlocklist::isEmailBlocked($this->input('email'))) {
            // Silently reject blocked emails by clearing the data
            // This prevents giving feedback to spammers
            $this->merge([
                'email' => 'blocked@example.com',
                'message' => '[Blocked submission]',
            ]);
        }
    }

    /**
     * Get validated contact data for storage.
     *
     * @return array<string, mixed>
     */
    public function contactData(): array
    {
        return [
            'name' => $this->validated('name'),
            'email' => $this->validated('email'),
            'subject' => $this->validated('subject'),
            'message' => $this->validated('message'),
            'sender_ip' => $this->ip(),
            'status' => 'unread',
        ];
    }
}
