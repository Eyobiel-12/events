<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final class OrganisationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') && $user->can('view_any_organisation');
    }

    public function view(User $user, Organisation $organisation): bool
    {
        return $user->hasRole('admin') && $user->can('view_organisation');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin') && $user->can('create_organisation');
    }

    public function update(User $user, Organisation $organisation): bool
    {
        return $user->hasRole('admin') && $user->can('update_organisation');
    }

    public function delete(User $user, Organisation $organisation): bool
    {
        return $user->hasRole('admin') && $user->can('delete_organisation');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('admin') && $user->can('delete_any_organisation');
    }
} 