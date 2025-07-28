<?php

namespace App\Http\Controllers\Workspaces;

use Illuminate\Http\Request;
use App\Models\Workspaces\WorkspaceInvitation;
use App\Models\Workspaces\WorkspaceUser;
use App\Http\Requests\InviteUserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

/**
 * @group Invitaciones del Workspace
 *
 * Endpoints para gestionar invitaciones a un workspace.
 */
class WorkspaceInvitationController extends Controller
{
    /**
     * Listar invitaciones del workspace.
     * @authenticated
     *
     * @urlParam workspace_id int required El ID del workspace. Example: 1
     *
     * @response 200{}
     */
    public function index($workspace_id)
    {
        //
    }

    /**
     * Enviar invitación a un usuario.
     * @authenticated
     *
     * @urlParam workspace_id int required El ID del workspace. Example: 1
     * @bodyParam email string required El correo electrónico del usuario a invitar. Example: "invitee@example.com"
     *
     * @response 201{}
     */
    public function store(Request $request, $workspace_id)
    {
        // Lógica para enviar una invitación
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
     * Cancelar invitación.
     * @authenticated
     *
     * @urlParam workspace_id int required El ID del workspace. Example: 1
     * @urlParam invitation_id int required El ID de la invitación a cancelar. Example: 2
     *
     * @response 204 {}
     */
    public function destroy($workspace_id, $invitation_id)
    {
        // Lógica para cancelar una invitación
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
            return response()->json([
                'message' => __('messages.unauthorized_workspace_invite')
            ], Response::HTTP_FORBIDDEN);
        }

        $invitation = WorkspaceInvitation::create([
            'workspace_id' => $workspaceId,
            'invited_by' => $user->id,
            'email' => $request->email,
            'role' => $request->role,
            'status' => 'pending',
        ]);

        return response()->json($invitation, Response::HTTP_CREATED);
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

        return response()->json([
            'message' => __('messages.invitation_accepted')
        ], Response::HTTP_OK);
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

        return response()->json([
            'message' => __('messages.invitation_declined')
        ], Response::HTTP_OK);
    }
}
