<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class ApiTokenManager extends Component
{
    public string $name = '';

    public ?string $plainTextToken = null;

    public string $message = '';

    public string $messageType = 'success';

    protected array $rules = [
        'name' => 'required|string|max:255',
    ];

    public function create(): void
    {
        if (! auth()->user()?->can('create posts')) {
            $this->setMessage('You do not have permission to create API tokens.', 'error');

            return;
        }

        $this->validate();

        $token = auth()->user()?->createToken($this->name, ['*']);

        if (! $token) {
            $this->setMessage('Unable to create token.', 'error');

            return;
        }

        $this->plainTextToken = $token->plainTextToken;
        $this->setMessage("Token '{$this->name}' created. Copy it now — it will not be shown again.", 'success');
        $this->name = '';
        $this->resetValidation();
    }

    public function revoke(int $tokenId): void
    {
        if (! auth()->user()?->can('create posts')) {
            $this->setMessage('You do not have permission to revoke API tokens.', 'error');

            return;
        }

        $token = auth()->user()?->tokens()->find($tokenId);

        if (! $token) {
            $this->setMessage('Token not found.', 'error');

            return;
        }

        $name = $token->name;
        $token->delete();

        $this->setMessage("Token '{$name}' revoked.", 'success');
    }

    public function dismissToken(): void
    {
        $this->plainTextToken = null;
    }

    private function setMessage(string $message, string $type): void
    {
        $this->message = $message;
        $this->messageType = $type;
    }

    public function render(): View
    {
        $tokens = auth()->user()?->tokens ?? collect();

        return view('livewire.api-token-manager', [
            'tokens' => $tokens,
        ]);
    }
}
