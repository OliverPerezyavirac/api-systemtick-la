<?php

namespace App\Http\Controllers\WorkspacesUsers;

use Illuminate\Http\Request;
use App\Models\Workspaces\WorkspaceUser;
use App\Http\Requests\StoreWorkspaceUserRequest;
use App\Http\Requests\UpdateWorkspaceUserRequest;
use App\Models\Workspaces\Workspace;
use App\Models\Users\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\WorkspaceInvitationNotification;
use Illuminate\Http\Response;

/**
 * @group Usuarios del Workspace
 *
 * Endpoints para gestionar usuarios dentro de un workspace.
 */
class WorkspaceUserController extends Controller
{
    /**
     * Listar usuarios del workspace.
     * @authenticated
     *
     * @urlParam workspace_id int required El ID del workspace. Example: 1
     *
     * @response 200{}
     */
    public function index($workspaceId)
    {
        $workspace = Workspace::findOrFail($workspaceId);
        $users = $workspace->users()
            ->select('users.id', 'users.first_name', 'users.last_name', 'users.email', 'workspace_users.role')
            ->withPivot('role')
            ->get();
        return response()->json($users);
    }

    /**
     * Agregar usuario al workspace.
     * @authenticated
     *
     * @urlParam workspace_id int required El ID del workspace. Example: 1
     * @bodyParam email string required El correo electrónico del usuario a agregar. Example: "john@example.com"
     * @bodyParam role string required El rol del usuario en el workspace. Example: "member"
     *
     * @response 201{}
     */
    public function store(Request $request, $workspaceId)
    {
        $currentUser = Auth::user();
        $workspace = Workspace::findOrFail($workspaceId);
        
        if (!$currentUser->hasDepartment($workspace->department_id)) {
            return response()->json([
                'message' => __('messages.not_in_workspace_department')
            ], Response::HTTP_FORBIDDEN);
        }
        
        $departmentRole = $currentUser->getDepartmentRole($workspace->department_id);
        if (!in_array($departmentRole, ['manager', 'admin'])) {
            return response()->json([
                'message' => __('messages.only_admin_manager_add_workspace_user')
            ], Response::HTTP_FORBIDDEN);
        }
        
        $workspaceRole = $workspace->users()
            ->where('user_id', $currentUser->id)
            ->first()
            ->pivot
            ->role ?? null;
            
        if (!in_array($workspaceRole, ['admin', 'manager'])) {
            return response()->json([
                'message' => __('messages.only_admin_manager_workspace_add_user')
            ], Response::HTTP_FORBIDDEN);
        }

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => 'required|in:member,manager'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($workspace->users()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => __('messages.user_already_in_workspace')
            ], Response::HTTP_CONFLICT);
        }

        $workspace->users()->attach($user->id, ['role' => $request->role]);

        $user->notify(new WorkspaceInvitationNotification($workspace, $request->role, $currentUser));

        return response()->json([
            'message' => __('messages.user_added_to_workspace'),
            'user' => $user,
            'role' => $request->role
        ], Response::HTTP_CREATED);
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
     * Actualizar rol de usuario en el workspace.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $workspaceId
     * @param  int  $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $workspaceId, $userId)
    {
        $request->validate([
            'role' => 'required|in:member,manager'
        ]);

        $workspace = Workspace::findOrFail($workspaceId);
        $workspace->users()->updateExistingPivot($userId, ['role' => $request->role]);

        return response()->json([
            'message' => __('messages.role_updated_successfully')
        ]);
    }

    /**
     * Eliminar usuario del workspace.
     * @authenticated
     *
     * @urlParam workspace_id int required El ID del workspace. Example: 1
     * @urlParam user_id int required El ID del usuario a eliminar. Example: 2
     *
     * @response 204 {}
     */
    public function destroy($workspaceId, $userId)
    {
        $user = Auth::user();
        
        if (!$user->canAccessWorkspace($workspaceId)) {
            return response()->json([
                'message' => __('messages.error_access_workspace')
            ], Response::HTTP_FORBIDDEN);
        }

        if ($userId == $user->id) {
            $adminCount = DB::table('workspace_users')
                ->where('workspace_id', $workspaceId)
                ->where('role', 'admin')
                ->count();

            // Si es el único admin, no permitir la eliminación
            if ($adminCount <= 1) {
                return response()->json([
                    'message' => __('messages.cannot_remove_self_if_only_admin')
                ], Response::HTTP_FORBIDDEN);
            }
        }

        // Verificar si el usuario tiene permisos para eliminar usuarios
        if (!$user->hasWorkspaceRole($workspaceId, ['admin'])) {
            return response()->json([
                'message' => __('messages.only_admin_remove_workspace_user')
            ], Response::HTTP_FORBIDDEN);
        }

        DB::table('workspace_users')
            ->where('workspace_id', $workspaceId)
            ->where('user_id', $userId)
            ->delete();

        return response()->json([
            'message' => __('messages.user_removed_from_workspace')
        ]);
    }
}
