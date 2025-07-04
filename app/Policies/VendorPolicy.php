<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Vendor;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final class VendorPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        if ($user->hasRole('admin')) {
            return $user->can('view_any_vendor');
        }

        if ($user->hasRole('organizer')) {
            return $user->can('view_own_vendors');
        }

        return false;
    }

    public function view(User $user, Vendor $vendor): bool
    {
        if ($user->hasRole('admin')) {
            return $user->can('view_vendor');
        }

        if ($user->hasRole('organizer')) {
            return $user->can('view_own_vendor') && $this->belongsToUserOrganisation($user, $vendor);
        }

        return false;
    }

    public function create(User $user): bool
    {
        if ($user->hasRole('admin')) {
            return $user->can('create_vendor');
        }

        if ($user->hasRole('organizer')) {
            return $user->can('create_own_vendor');
        }

        return false;
    }

    public function update(User $user, Vendor $vendor): bool
    {
        if ($user->hasRole('admin')) {
            return $user->can('update_vendor');
        }

        if ($user->hasRole('organizer')) {
            return $user->can('update_own_vendor') && $this->belongsToUserOrganisation($user, $vendor);
        }

        return false;
    }

    public function delete(User $user, Vendor $vendor): bool
    {
        if ($user->hasRole('admin')) {
            return $user->can('delete_vendor');
        }

        if ($user->hasRole('organizer')) {
            return $user->can('delete_own_vendor') && $this->belongsToUserOrganisation($user, $vendor);
        }

        return false;
    }

    public function deleteAny(User $user): bool
    {
        if ($user->hasRole('admin')) {
            return $user->can('delete_any_vendor');
        }

        return false;
    }

    private function belongsToUserOrganisation(User $user, Vendor $vendor): bool
    {
        return $user->organisations()
            ->where('organisation_id', $vendor->organisation_id)
            ->exists();
    }
} 