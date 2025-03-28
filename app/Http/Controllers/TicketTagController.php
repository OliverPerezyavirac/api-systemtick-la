<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Ticket;
use Illuminate\Http\Request;

/**
 * @group Tags de Tickets
 *
 * Endpoints para gestionar tags en tickets.
 */
class TicketTagController extends Controller
{
    /**
     * Asignar tags a un ticket.
     * @authenticated
     *
     * @urlParam ticket_id int required El ID del ticket. Example: 1
     * @bodyParam tags array required Los IDs de los tags a asignar. Example: [1, 2, 3]
     *
     * @response 200 {}
     */
    public function store(Request $request, $ticket_id)
    {
        $validated = $request->validate([
            'tags' => 'required|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $tags = Tag::whereIn('id', $validated['tags'])
            ->where('workspace_id', $ticket_id)
            ->get();

        $ticket = Ticket::findOrFail($ticket_id);
        $ticket->tags()->syncWithoutDetaching($tags);

        return response()->json($ticket->tags);
    }

    /**
     * Remover un tag de un ticket.
     * @authenticated
     *
     * @urlParam ticket_id int required El ID del ticket. Example: 1
     * @urlParam tag_id int required El ID del tag a remover. Example: 2
     *
     * @response 204 {}
     */
    public function destroy($ticket_id, $tag_id)
    {
        $ticket = Ticket::findOrFail($ticket_id);
        $tag = Tag::findOrFail($tag_id);

        if ($tag->workspace_id !== $ticket->workspace_id) {
            return response()->json(['error' => 'Tag does not belong to the same workspace'], 403);
        }

        $ticket->tags()->detach($tag);

        return response()->json(null, 204);
    }
}
