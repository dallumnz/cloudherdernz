<?php

namespace App\Policies;

use App\Models\Taxonomy;
use App\Models\User;

class TaxonomyPolicy
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
        return $user->can('view categories');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Taxonomy $taxonomy): bool
    {
        return $user->can('view categories');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create categories');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Taxonomy $taxonomy): bool
    {
        return $user->can('edit categories');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Taxonomy $taxonomy): bool
    {
        return $user->can('delete categories');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Taxonomy $taxonomy): bool
    {
        return $user->can('delete categories');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Taxonomy $taxonomy): bool
    {
        return $user->can('delete categories');
    }
}
