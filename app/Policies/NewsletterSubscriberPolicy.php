<?php

namespace App\Policies;

use App\Models\NewsletterSubscriber;
use App\Models\User;

class NewsletterSubscriberPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view newsletter subscribers');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, NewsletterSubscriber $subscriber): bool
    {
        return $user->can('view newsletter subscribers');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create newsletter subscribers');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, NewsletterSubscriber $subscriber): bool
    {
        return $user->can('edit newsletter subscribers');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, NewsletterSubscriber $subscriber): bool
    {
        return $user->can('delete newsletter subscribers');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, NewsletterSubscriber $subscriber): bool
    {
        return $user->can('delete newsletter subscribers');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, NewsletterSubscriber $subscriber): bool
    {
        return $user->can('delete newsletter subscribers');
    }

    /**
     * Determine whether the user can export subscribers.
     */
    public function export(User $user): bool
    {
        return $user->can('export newsletter subscribers');
    }

    /**
     * Determine whether the user can manage subscriber status.
     */
    public function manageStatus(User $user, NewsletterSubscriber $subscriber): bool
    {
        return $user->can('edit newsletter subscribers');
    }
}
