<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

class LogSuccessfulLogin
{
    public function __construct(private Request $request) {}

    public function handle(Login $event): void
    {
        activity()
            ->causedBy($event->user)
            ->withProperties([
                'ip' => $this->request->ip(),
                'user_agent' => $this->request->userAgent(),
            ])
            ->event('login')
            ->log("User {$event->user->name} logged in");
    }
}
