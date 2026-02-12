<?php

namespace App\Policies;

use App\Models\TaxonomyTerm;
use App\Models\User;

class TaxonomyTermPolicy
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
        return $user->can('view tags') || $user->can('view categories');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TaxonomyTerm $taxonomyTerm): bool
    {
        $taxonomy = $taxonomyTerm->taxonomy;

        if ($taxonomy->type === 'tag') {
            return $user->can('view tags');
        }

        return $user->can('view categories');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Check based on the taxonomy type being created
        return $user->can('create tags') || $user->can('create categories');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TaxonomyTerm $taxonomyTerm): bool
    {
        $taxonomy = $taxonomyTerm->taxonomy;

        if ($taxonomy->type === 'tag') {
            return $user->can('edit tags');
        }

        return $user->can('edit categories');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaxonomyTerm $taxonomyTerm): bool
    {
        $taxonomy = $taxonomyTerm->taxonomy;

        if ($taxonomy->type === 'tag') {
            return $user->can('delete tags');
        }

        return $user->can('delete categories');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TaxonomyTerm $taxonomyTerm): bool
    {
        $taxonomy = $taxonomyTerm->taxonomy;

        if ($taxonomy->type === 'tag') {
            return $user->can('delete tags');
        }

        return $user->can('delete categories');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TaxonomyTerm $taxonomyTerm): bool
    {
        $taxonomy = $taxonomyTerm->taxonomy;

        if ($taxonomy->type === 'tag') {
            return $user->can('delete tags');
        }

        return $user->can('delete categories');
    }
}
