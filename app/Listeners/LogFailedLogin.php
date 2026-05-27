<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Http\Request;

class LogFailedLogin
{
    public function __construct(private Request $request) {}

    public function handle(Failed $event): void
    {
        $email = $event->credentials['email'] ?? 'unknown';

        activity()
            ->withProperties([
                'ip' => $this->request->ip(),
                'user_agent' => $this->request->userAgent(),
                'email' => $email,
            ])
            ->event('failed_login')
            ->log("Failed login attempt for {$email}");
    }
}
