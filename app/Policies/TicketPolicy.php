<?php

namespace App\Policies;

use App\Models\Users\User;
use App\Models\Tickets\Ticket;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\Users\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Users\User  $user
     * @param  \App\Models\Tickets\Ticket  $ticket
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Ticket $ticket)
    {
        // El usuario puede ver el ticket si:
        // 1. Es el creador del ticket
        // 2. Es el asignado del ticket
        // 3. Tiene rol de admin o manager en el workspace
        return $ticket->creator_id === $user->id || 
               $ticket->assignee_id === $user->id ||
               $user->hasWorkspaceRole($ticket->workspace_id, ['admin', 'manager']);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Users\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Users\User  $user
     * @param  \App\Models\Tickets\Ticket  $ticket
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Ticket $ticket)
    {
        // El usuario puede actualizar el ticket si:
        // 1. Es el creador del ticket
        // 2. Es el asignado del ticket
        // 3. Tiene rol de admin o manager en el workspace
        return $ticket->creator_id === $user->id || 
               $ticket->assignee_id === $user->id ||
               $user->hasWorkspaceRole($ticket->workspace_id, ['admin', 'manager']);
    }

    /**
     * Determine whether the user can update the title of the ticket.
     *
     * @param  \App\Models\Users\User  $user
     * @param  \App\Models\Tickets\Ticket  $ticket
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function updateTitle(User $user, Ticket $ticket)
    {
        // Solo el creador del ticket o usuarios con rol admin/manager pueden cambiar el título
        return $ticket->creator_id === $user->id || 
               $user->hasWorkspaceRole($ticket->workspace_id, ['admin', 'manager']);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Users\User  $user
     * @param  \App\Models\Tickets\Ticket  $ticket
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Ticket $ticket)
    {
        // Solo el creador del ticket o usuarios con rol admin/manager pueden eliminar
        return $ticket->creator_id === $user->id || 
               $user->hasWorkspaceRole($ticket->workspace_id, ['admin', 'manager']);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Users\User  $user
     * @param  \App\Models\Tickets\Ticket  $ticket
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Ticket $ticket)
    {
        return $ticket->creator_id === $user->id || 
               $user->hasWorkspaceRole($ticket->workspace_id, ['admin', 'manager']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\Users\User  $user
     * @param  \App\Models\Tickets\Ticket  $ticket
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Ticket $ticket)
    {
        return $ticket->creator_id === $user->id || 
               $user->hasWorkspaceRole($ticket->workspace_id, ['admin', 'manager']);
    }

    /**
     * Determine whether the user can assign the ticket.
     *
     * @param  \App\Models\Users\User  $user
     * @param  \App\Models\Tickets\Ticket  $ticket
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function assign(User $user, Ticket $ticket)
    {
        // Solo usuarios con rol admin o manager pueden asignar tickets
        return $user->hasWorkspaceRole($ticket->workspace_id, ['admin', 'manager']);
    }

    /**
     * Determine whether the user can change the status of the ticket.
     *
     * @param  \App\Models\Users\User  $user
     * @param  \App\Models\Tickets\Ticket  $ticket
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function changeStatus(User $user, Ticket $ticket)
    {
        // El usuario puede cambiar el estado si:
        // 1. Es el creador del ticket
        // 2. Es el asignado del ticket
        // 3. Tiene rol de admin o manager en el workspace
        return $ticket->creator_id === $user->id || 
               $ticket->assignee_id === $user->id ||
               $user->hasWorkspaceRole($ticket->workspace_id, ['admin', 'manager']);
    }
} 