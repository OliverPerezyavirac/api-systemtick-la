<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use Illuminate\Support\Facades\Auth;
use App\Events\TicketAssigned;
use App\Models\User;

/**
 * @group Tickets
 *
 * Endpoints para gestionar tickets.
 */
class TicketController extends Controller
{
    /**
     * Listar tickets.
     * @authenticated
     *
     * @queryParam page int Página de resultados. Example: 1
     * @queryParam per_page int Número de resultados por página. Example: 10
     *
     * @response 200 {}
     */
    public function index($workspaceId)
    {
        $tickets = Ticket::where('workspace_id', $workspaceId)->get();
        return response()->json($tickets);
    }

    /**
     * Crear ticket.
     * @authenticated
     *
     * @bodyParam title string required El título del ticket. Example: "Problema con el servidor"
     * @bodyParam description string La descripción del problema. Example: "El servidor no responde"
     *
     * @response 201 {}
     */
    public function store(StoreTicketRequest $request, $workspaceId)
    {
        $ticket = Ticket::create(array_merge(
            $request->validated(),
            ['creator_id' => Auth::id(), 'workspace_id' => $workspaceId]
        ));

        if ($ticket->assignee_id) {
            event(new TicketAssigned($ticket));
            \Log::info("Evento de asignación disparado para ticket {$ticket->id}");
        }

        return response()->json($ticket, 201);
    }

    /**
     * Mostrar ticket.
     * @authenticated
     *
     * @urlParam id int required El ID del ticket. Example: 1
     *
     * @response 200 {}
     */
    public function show($workspaceId, $ticketId)
    {
        $ticket = Ticket::where('workspace_id', $workspaceId)->findOrFail($ticketId);
        return response()->json($ticket);
    }

    /**
     * Actualizar ticket.
     * @authenticated
     *
     * @urlParam id int required El ID del ticket. Example: 1
     * @bodyParam title string El nuevo título del ticket. Example: "Problema con el servidor actualizado"
     * @bodyParam status string El nuevo estado del ticket. Example: "cerrado"
     *
     * @response 200 {}
     */
    public function update(UpdateTicketRequest $request, $workspaceId, $ticketId)
    {
        $ticket = Ticket::where('workspace_id', $workspaceId)->findOrFail($ticketId);

        if ($ticket->creator_id !== Auth::id() && !in_array(Auth::user()->role, ['admin', 'manager'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $ticket->update($request->validated());

        return response()->json($ticket);
    }

    /**
     * Eliminar ticket.
     * @authenticated
     *
     * @urlParam id int required El ID del ticket. Example: 1
     *
     * @response 204 {}
     */
    public function destroy($workspaceId, $ticketId)
    {
        $ticket = Ticket::where('workspace_id', $workspaceId)->findOrFail($ticketId);

        if (!in_array(Auth::user()->role, ['admin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $ticket->delete();

        return response()->json(null, 204);
    }

    /**
     * Assign a ticket to a user.
     *
     * @param  int  $workspaceId
     * @param  int  $ticketId
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function assign($workspaceId, $ticketId, Request $request)
    {
        $ticket = Ticket::where('workspace_id', $workspaceId)->findOrFail($ticketId);

        if (!in_array(Auth::user()->role, ['admin', 'manager'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        \Log::info('Asignando ticket', [
            'ticket_id' => $ticketId,
            'new_assignee' => $request->assignee_id,
            'current_user' => Auth::id()
        ]);
        
        $ticket->update(['assignee_id' => $request->assignee_id]);

        event(new TicketAssigned($ticket, $ticket->assignee));
        \Log::info('Ticket assigned to ' . $ticket->assignee_id);

        return response()->json($ticket);
    }

    /**
     * Change the status of a ticket.
     *
     * @param  int  $workspaceId
     * @param  int  $ticketId
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changeStatus($workspaceId, $ticketId, Request $request)
    {
        $ticket = Ticket::where('workspace_id', $workspaceId)->findOrFail($ticketId);

        $ticket->update(['status' => $request->status]);

        return response()->json($ticket);
    }
}
