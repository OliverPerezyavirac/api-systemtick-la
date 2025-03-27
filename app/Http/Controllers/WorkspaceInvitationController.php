<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkspaceInvitation;
use App\Models\WorkspaceUser;
use App\Http\Requests\InviteUserRequest;
use Illuminate\Support\Facades\Auth;

class WorkspaceInvitationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * Invite a user to a workspace.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $workspaceId
     * @return \Illuminate\Http\Response
     */
    public function invite(InviteUserRequest $request, $workspaceId)
    {
        $user = Auth::user();
        $role = WorkspaceUser::where('workspace_id', $workspaceId)
            ->where('user_id', $user->id)
            ->value('role');

        if (!in_array($role, ['admin', 'manager'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $invitation = WorkspaceInvitation::create([
            'workspace_id' => $workspaceId,
            'invited_by' => $user->id,
            'email' => $request->email,
            'role' => $request->role,
            'status' => 'pending',
        ]);

        return response()->json($invitation, 201);
    }

    /**
     * Accept the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function accept($invitationId)
    {
        $invitation = WorkspaceInvitation::findOrFail($invitationId);

        WorkspaceUser::create([
            'workspace_id' => $invitation->workspace_id,
            'user_id' => Auth::id(),
            'role' => $invitation->role,
            'joined_at' => now(),
        ]);

        $invitation->delete();

        return response()->json(['message' => 'Invitation accepted'], 200);
    }

    /**
     * Decline the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function decline($invitationId)
    {
        $invitation = WorkspaceInvitation::findOrFail($invitationId);
        $invitation->update(['status' => 'declined']);

        return response()->json(['message' => 'Invitation declined'], 200);
    }
}
