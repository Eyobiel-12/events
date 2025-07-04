<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final class TicketPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        if ($user->hasRole('admin')) {
            return $user->can('view_any_ticket');
        }

        if ($user->hasRole('organizer')) {
            return $user->can('view_own_tickets');
        }

        return false;
    }

    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->hasRole('admin')) {
            return $user->can('view_ticket');
        }

        if ($user->hasRole('organizer')) {
            return $user->can('view_own_ticket') && $this->belongsToUserOrganisation($user, $ticket);
        }

        return false;
    }

    public function create(User $user): bool
    {
        if ($user->hasRole('admin')) {
            return $user->can('create_ticket');
        }

        if ($user->hasRole('organizer')) {
            return $user->can('create_own_ticket');
        }

        return false;
    }

    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->hasRole('admin')) {
            return $user->can('update_ticket');
        }

        if ($user->hasRole('organizer')) {
            return $user->can('update_own_ticket') && $this->belongsToUserOrganisation($user, $ticket);
        }

        return false;
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        if ($user->hasRole('admin')) {
            return $user->can('delete_ticket');
        }

        if ($user->hasRole('organizer')) {
            return $user->can('delete_own_ticket') && $this->belongsToUserOrganisation($user, $ticket);
        }

        return false;
    }

    public function deleteAny(User $user): bool
    {
        if ($user->hasRole('admin')) {
            return $user->can('delete_any_ticket');
        }

        return false;
    }

    private function belongsToUserOrganisation(User $user, Ticket $ticket): bool
    {
        return $user->organisations()
            ->where('organisation_id', $ticket->ticketType->event->organisation_id)
            ->exists();
    }
} 