<?php

namespace App\Policies;

use App\Models\SupportTicket;
use App\Models\User;

class SupportTicketPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isStudent() || $user->can('tickets.view');
    }

    public function create(User $user): bool
    {
        // Students file their own tickets. Staff can ALSO proactively open one
        // TO a student (e.g. "we noticed X, please clarify Y") — tickets.respond
        // is the right gate since it's the same permission that lets you
        // participate in a ticket at all.
        return $user->isStudent() || $user->can('tickets.respond');
    }

    public function view(User $user, SupportTicket $ticket): bool
    {
        if ($user->isStudent()) {
            return $ticket->student_id === $user->id;
        }

        return $user->can('tickets.view');
    }

    public function respond(User $user, SupportTicket $ticket): bool
    {
        if ($user->isStudent()) {
            return $ticket->student_id === $user->id && $ticket->status !== 'closed';
        }

        return $user->can('tickets.respond');
    }

    public function close(User $user, SupportTicket $ticket): bool
    {
        return $user->can('tickets.close') && ! $user->isStudent();
    }
}
