<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketTagController extends Controller
{
    /**
     * Add tags to a ticket.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'tags' => 'required|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $tags = Tag::whereIn('id', $validated['tags'])
            ->where('workspace_id', $ticket->workspace_id)
            ->get();

        $ticket->tags()->syncWithoutDetaching($tags);

        return response()->json($ticket->tags);
    }

    /**
     * Remove a tag from a ticket.
     *
     * @param  \App\Models\Ticket  $ticket
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ticket $ticket, Tag $tag)
    {
        if ($tag->workspace_id !== $ticket->workspace_id) {
            return response()->json(['error' => 'Tag does not belong to the same workspace'], 403);
        }

        $ticket->tags()->detach($tag);

        return response()->json(null, 204);
    }
}
