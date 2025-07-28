<?php

namespace App\Http\Controllers\Tickets;

use Illuminate\Http\Request;
use App\Models\Tickets\Ticket;
use App\Http\Requests\Tickets\StoreTicketRequest;
use App\Http\Requests\Tickets\UpdateTicketRequest;
use Illuminate\Support\Facades\Auth;
use App\Events\TicketAssigned;
use App\Models\Users\User;
use App\Models\Workspaces\Workspace;
use App\Models\Tickets\TicketStatus;
use App\Services\ClaudeService;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

/**
 * @group Tickets
 *
 * Endpoints para gestionar tickets.
 */
class TicketController extends Controller
{
    protected $claudeService;

    public function __construct(ClaudeService $claudeService)
    {
        $this->claudeService = $claudeService;
    }

    /**
     * Listar tickets.
     * @authenticated
     *
     * @queryParam page int Página de resultados. Example: 1
     * @queryParam per_page int Número de resultados por página. Example: 10
     * @queryParam assigned boolean Filtrar tickets asignados (true) o no asignados (false). Example: true
     *
     * @response 200 {}
     */
    public function index($workspaceId, Request $request)
    {
        $query = Ticket::where('workspace_id', $workspaceId)
            ->with(['assignee' => function($query) {
                $query->select('id', 'first_name', 'last_name', 'email');
            }, 'creator' => function($query) {
                $query->select('id', 'first_name', 'last_name', 'email');
            }]);

        if ($request->has('assigned')) {
            if ($request->assigned === 'true') {
                $query->whereNotNull('assignee_id');
            } else {
                $query->whereNull('assignee_id');
            }
        }

        $tickets = $query->get();
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
        try {
            $user = Auth::user();
            
            if (!$user->hasWorkspaceRole($workspaceId, ['admin', 'manager'])) {
                return response()->json([
                    'message' => __('messages.only_admin_manager_create_ticket')
                ], Response::HTTP_FORBIDDEN);
            }

            $workspace = Workspace::findOrFail($workspaceId);
            $assignee = null;
            
            if ($request->has('assignee_email')) {
                $assignee = $workspace->users()->where('email', $request->assignee_email)->first();
                if (!$assignee) {
                    return response()->json([
                        'message' => __('messages.assignee_not_in_workspace')
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            }

            $ticket = Ticket::create(array_merge(
                $request->validated(),
                [
                    'creator_id' => Auth::id(),
                    'workspace_id' => $workspaceId,
                    'assignee_id' => $assignee ? $assignee->id : null
                ]
            ));

            if ($ticket->assignee_id) {
                try {
                    event(new TicketAssigned($ticket));
                    \Log::info("Evento de asignación disparado para ticket {$ticket->id}");
                } catch (\Exception $e) {
                    \Log::error("Error al enviar notificación de asignación: " . $e->getMessage());
                }
            }

            return response()->json($ticket, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            \Log::error("Error al crear ticket: " . $e->getMessage());
            return response()->json([
                'message' => __('messages.error_creating_ticket'),
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        $ticket = Ticket::where('workspace_id', $workspaceId)
            ->with(['assignee' => function($query) {
                $query->select('id', 'first_name', 'last_name', 'email');
            }, 'creator' => function($query) {
                $query->select('id', 'first_name', 'last_name', 'email');
            }])
            ->findOrFail($ticketId);
            
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
        $user = Auth::user();

        if (!$user->hasWorkspaceRole($workspaceId, ['admin', 'manager'])) {
            return response()->json([
                'message' => __('messages.only_admin_manager_update_ticket')
            ], Response::HTTP_FORBIDDEN);
        }

        if ($request->has('status') && $request->status !== $ticket->status) {
            if (!TicketStatus::isValidTransition($ticket->status, $request->status)) {
                $validTransitions = TicketStatus::getValidTransitions($ticket->status);
                return response()->json([
                    'message' => __('messages.invalid_status_transition'),
                    'current_status' => $ticket->status,
                    'valid_transitions' => $validTransitions
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
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
        $user = Auth::user();

        if (!$user->hasWorkspaceRole($workspaceId, ['admin', 'manager'])) {
            return response()->json([
                'message' => __('messages.only_admin_manager_delete_ticket')
            ], Response::HTTP_FORBIDDEN);
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
        $user = Auth::user();

        if (!$user->hasWorkspaceRole($workspaceId, ['admin', 'manager'])) {
            return response()->json([
                'message' => __('messages.only_admin_manager_assign_ticket')
            ], Response::HTTP_FORBIDDEN);
        }

        $workspace = Workspace::findOrFail($workspaceId);
        $assignee = $workspace->users()->find($request->assignee_id);
        
        if (!$assignee) {
            return response()->json([
                'message' => __('messages.assignee_not_in_workspace')
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        \Log::info('Asignando ticket', [
            'ticket_id' => $ticketId,
            'new_assignee' => $request->assignee_id,
            'current_user' => $user->id
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
        $user = Auth::user();

        if ($ticket->creator_id !== $user->id && 
            $ticket->assignee_id !== $user->id && 
            !$user->hasWorkspaceRole($workspaceId, ['admin', 'manager'])) {
            return response()->json([
                'message' => __('messages.only_creator_assignee_admin_change_status')
            ], Response::HTTP_FORBIDDEN);
        }
        
        $newStatus = $request->status;
        
        if (!TicketStatus::isValidTransition($ticket->status, $newStatus)) {
            $validTransitions = TicketStatus::getValidTransitions($ticket->status);
            return response()->json([
                'message' => __('messages.invalid_status_transition'),
                'current_status' => $ticket->status,
                'valid_transitions' => $validTransitions
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $ticket->update(['status' => $newStatus]);

        return response()->json($ticket);
    }


    public function getAISuggestions($workspaceId, $ticketId)
    {
        $ticket = Ticket::where('workspace_id', $workspaceId)->findOrFail($ticketId);
        $user = Auth::user();

        if ($ticket->creator_id !== $user->id && 
            $ticket->assignee_id !== $user->id && 
            !$user->hasWorkspaceRole($workspaceId, ['admin', 'manager'])) {
            return response()->json([
                'message' => __('messages.only_creator_assignee_admin_view_ticket')
            ], Response::HTTP_FORBIDDEN);
        }

        $solutions = $this->claudeService->analyzeTicket($ticket->description);

        if (!$solutions) {
            return response()->json([
                'message' => __('messages.error_generating_ai_suggestions')
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'solutions' => $solutions
        ]);
    }
}
