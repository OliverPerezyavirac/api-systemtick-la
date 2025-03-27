<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use Illuminate\Support\Facades\Auth;
use App\Events\TicketAssigned;
use App\Models\User;


class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($workspaceId)
    {
        $tickets = Ticket::where('workspace_id', $workspaceId)->get();
        return response()->json($tickets);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($workspaceId, $ticketId)
    {
        $ticket = Ticket::where('workspace_id', $workspaceId)->findOrFail($ticketId);
        return response()->json($ticket);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
