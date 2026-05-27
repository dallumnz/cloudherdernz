<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;

class LogLogout
{
    public function __construct(private Request $request) {}

    public function handle(Logout $event): void
    {
        if (! $event->user) {
            return;
        }

        activity()
            ->causedBy($event->user)
            ->withProperties([
                'ip' => $this->request->ip(),
                'user_agent' => $this->request->userAgent(),
            ])
            ->event('logout')
            ->log("User {$event->user->name} logged out");
    }
}
