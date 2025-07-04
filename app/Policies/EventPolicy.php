<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final class EventPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        if ($user->hasRole('admin')) {
            return $user->can('view_any_event');
        }

        if ($user->hasRole('organizer')) {
            return $user->can('view_own_events');
        }

        return false;
    }

    public function view(User $user, Event $event): bool
    {
        if ($user->hasRole('admin')) {
            return $user->can('view_event');
        }

        if ($user->hasRole('organizer')) {
            return $user->can('view_own_event') && $this->belongsToUserOrganisation($user, $event);
        }

        return false;
    }

    public function create(User $user): bool
    {
        if ($user->hasRole('admin')) {
            return $user->can('create_event');
        }

        if ($user->hasRole('organizer')) {
            return $user->can('create_own_event');
        }

        return false;
    }

    public function update(User $user, Event $event): bool
    {
        if ($user->hasRole('admin')) {
            return $user->can('update_event');
        }

        if ($user->hasRole('organizer')) {
            return $user->can('update_own_event') && $this->belongsToUserOrganisation($user, $event);
        }

        return false;
    }

    public function delete(User $user, Event $event): bool
    {
        if ($user->hasRole('admin')) {
            return $user->can('delete_event');
        }

        if ($user->hasRole('organizer')) {
            return $user->can('delete_own_event') && $this->belongsToUserOrganisation($user, $event);
        }

        return false;
    }

    public function deleteAny(User $user): bool
    {
        if ($user->hasRole('admin')) {
            return $user->can('delete_any_event');
        }

        return false;
    }

    private function belongsToUserOrganisation(User $user, Event $event): bool
    {
        return $user->organisations()
            ->where('organisation_id', $event->organisation_id)
            ->exists();
    }
} 