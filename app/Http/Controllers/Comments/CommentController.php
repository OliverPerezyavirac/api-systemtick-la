<?php

namespace App\Http\Controllers\Comments;

use Illuminate\Http\Request;
use App\Models\Comments\Comment;
use App\Models\Tickets\Ticket;
use App\Http\Requests\StoreCommentRequest;
use Illuminate\Support\Facades\Auth;
use App\Notifications\TicketCommentNotification;
use Illuminate\Http\Response;

/**
 * @group Comentarios
 *
 * Endpoints para gestionar comentarios en tickets.
 */
class CommentController extends Controller
{
    /**
     * Listar comentarios de un ticket.
     * @authenticated
     *
     * @urlParam ticket_id int required El ID del ticket. Example: 1
     *
     * @response 200 {}
     */
    public function index($workspaceId, $ticketId)
    {
        $ticket = Ticket::where('workspace_id', $workspaceId)->findOrFail($ticketId);
        $user = Auth::user();

        $comments = Comment::where('ticket_id', $ticketId)
            ->when(!$this->canViewPrivateComments($ticket, $user), function ($query) {
                $query->where('visibility', 'public');
            })
            ->with('user')
            ->get();

        return response()->json($comments);
    }

    /**
     * Crear un comentario en un ticket.
     * @authenticated
     *
     * @urlParam ticket_id int required El ID del ticket. Example: 1
     * @bodyParam content string required El contenido del comentario. Example: "Este es un comentario."
     *
     * @response 201 {}
     */
    public function store(Request $request, $workspaceId, $ticketId)
    {
        try {
            $user = Auth::user();
            $ticket = Ticket::findOrFail($ticketId);

            if (!$user->canAccessWorkspace($workspaceId)) {
                return response()->json([
                    'message' => __("messages.error_access_workspace")
                ], Response::HTTP_FORBIDDEN);
            }

            $request->validate([
                'content' => 'required|string',
                'visibility' => 'required|in:public,private'
            ]);

            if ($request->visibility === 'private' && !$this->canViewPrivateComments($ticket, $user)) {
                return response()->json([
                    'message' => __("messages.error_private_comment_permission")
                ], Response::HTTP_FORBIDDEN);
            }

            $comment = $ticket->comments()->create([
                'content' => $request->content,
                'user_id' => $user->id,
                'visibility' => $request->visibility
            ]);

            $usersToNotify = collect();

            if ($ticket->creator) {
                $usersToNotify->push($ticket->creator);
            }

            if ($ticket->assignee) {
                $usersToNotify->push($ticket->assignee);
            }

            if ($request->visibility === 'public') {
                $usersToNotify = $usersToNotify->merge(
                    $ticket->comments()
                        ->with('user')
                        ->get()
                        ->pluck('user')
                );
            }

            $usersToNotify = $usersToNotify->unique('id')
                ->filter(function ($notifiedUser) use ($user) {
                    return $notifiedUser->id !== $user->id;
                });

            foreach ($usersToNotify as $userToNotify) {
                if ($userToNotify) {
                    try {
                        $userToNotify->notify(new TicketCommentNotification($ticket, $comment, $user));
                    } catch (\Exception $e) {
                        \Log::error("Error al enviar notificación de comentario: " . $e->getMessage());
                    }
                }
            }

            return response()->json([
                'message' => __("messages.comment_created"),
                'comment' => $comment->load('user')
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            \Log::error("Error al crear comentario: " . $e->getMessage());
            return response()->json([
                'message' => __("messages.error_creating_comment"),
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Eliminar un comentario.
     * @authenticated
     *
     * @urlParam ticket_id int required El ID del ticket. Example: 1
     * @urlParam comment_id int required El ID del comentario. Example: 1
     *
     * @response 204 {}
     */
    public function destroy($ticket_id, $comment_id)
    {
        //
    }

    /**
     * Check if the user can view private comments.
     *
     * @param  \App\Models\Ticket  $ticket
     * @param  \App\Models\User  $user
     * @return bool
     */
    private function canViewPrivateComments($ticket, $user)
    {
        return $ticket->creator_id === $user->id || $ticket->assignee_id === $user->id;
    }
}
