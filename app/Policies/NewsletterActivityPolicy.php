<?php

namespace App\Policies;

use App\Models\NewsletterActivity;
use App\Models\User;

class NewsletterActivityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['Admin', 'Editor']);
    }

    public function view(User $user, NewsletterActivity $activity): bool
    {
        return $user->hasRole(['Admin', 'Editor']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['Admin', 'Editor']);
    }

    public function update(User $user, NewsletterActivity $activity): bool
    {
        return $user->hasRole(['Admin', 'Editor']);
    }

    public function delete(User $user, NewsletterActivity $activity): bool
    {
        return $user->hasRole('Admin');
    }
}
