<?php

namespace App\Policies;

use App\Models\User;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaPolicy
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
     * Determine whether the user can view any media.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view media');
    }

    /**
     * Determine whether the user can view the media.
     */
    public function view(User $user, Media $media): bool
    {
        return $user->can('view media');
    }

    /**
     * Determine whether the user can create media.
     */
    public function create(User $user): bool
    {
        return $user->can('upload media');
    }

    /**
     * Determine whether the user can update the media.
     */
    public function update(User $user, Media $media): bool
    {
        return $user->can('edit media');
    }

    /**
     * Determine whether the user can delete the media.
     */
    public function delete(User $user, Media $media): bool
    {
        return $user->can('delete media');
    }

    /**
     * Determine whether the user can restore the media.
     */
    public function restore(User $user, Media $media): bool
    {
        return $user->can('delete media');
    }

    /**
     * Determine whether the user can permanently delete the media.
     */
    public function forceDelete(User $user, Media $media): bool
    {
        return $user->can('delete media');
    }

    /**
     * Determine whether the user can upload media.
     */
    public function upload(User $user): bool
    {
        return $user->can('upload media');
    }

    /**
     * Determine whether the user can manage media collections.
     */
    public function manageCollections(User $user): bool
    {
        return $user->can('edit media');
    }
}
