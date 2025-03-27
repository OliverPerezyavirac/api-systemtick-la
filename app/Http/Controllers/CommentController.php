<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Ticket;
use App\Http\Requests\StoreCommentRequest;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCommentRequest $request, $workspaceId, $ticketId)
    {
        $ticket = Ticket::where('workspace_id', $workspaceId)->findOrFail($ticketId);

        if ($request->visibility === 'private' && !$this->canViewPrivateComments($ticket, Auth::user())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment = Comment::create(array_merge(
            $request->validated(),
            ['user_id' => Auth::id(), 'ticket_id' => $ticketId]
        ));

        return response()->json($comment, 201);
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
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
