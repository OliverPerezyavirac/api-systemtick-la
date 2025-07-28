<?php

namespace App\Http\Controllers\Workspaces;

use Illuminate\Http\Request;
use App\Models\Workspaces\Workspace;
use App\Http\Requests\Workspaces\StoreWorkspaceRequest;
use App\Http\Requests\Workspaces\UpdateWorkspaceRequest;
use App\Models\Workspaces\WorkspaceUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

/**
 * @group Workspaces
 *
 * Endpoints para gestionar workspaces.
 */
class WorkspaceController extends Controller
{
    /**
     * Listar workspaces del usuario.
     * @authenticated
     *
     * @queryParam page int Página de resultados. Example: 1
     * @queryParam per_page int Número de resultados por página. Example: 10
     *
     * @response 200 {}
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $workspaces = Workspace::whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with(['department:id,name,description'])
        ->withCount('users')
        ->paginate($request->input('per_page', 10));

        foreach ($workspaces as $workspace) {
            $workspace->permissions = $this->getWorkspacePermissions($workspace, $user);
        }

        return response()->json($workspaces);
    }

    /**
     * Obtener los permisos del usuario en un workspace
     */
    private function getWorkspacePermissions(Workspace $workspace, $user)
    {
        // Obtener el rol del usuario en el departamento
        $departmentRole = $user->getDepartmentRole($workspace->department_id);
        
        // Obtener el rol del usuario en el workspace
        $workspaceUser = $workspace->users()->where('user_id', $user->id)->first();
        $workspaceRole = $workspaceUser ? $workspaceUser->pivot->role : null;
        
        // Verificar si el usuario pertenece al departamento
        $belongsToDepartment = $user->hasDepartment($workspace->department_id);
        
        // Verificar si el usuario tiene rol de manager o admin en el departamento
        $hasDepartmentAdminRole = in_array($departmentRole, ['manager', 'admin']);
        
        // Verificar si el usuario tiene rol de manager o admin en el workspace
        $hasWorkspaceAdminRole = in_array($workspaceRole, ['manager', 'admin']);
        
        // Si el usuario es el creador del workspace, siempre tiene permisos de admin
        if ($workspace->owner_id === $user->id) {
            $workspaceRole = 'admin';
            $hasWorkspaceAdminRole = true;
        }
        
        return [
            'can_create' => $belongsToDepartment && $hasDepartmentAdminRole,
            'can_update' => $belongsToDepartment && $hasDepartmentAdminRole,
            'can_delete' => $belongsToDepartment && $hasDepartmentAdminRole,
            'can_manage_users' => $hasWorkspaceAdminRole,
            'can_add_users' => $belongsToDepartment && $hasDepartmentAdminRole && $hasWorkspaceAdminRole,
            'department_role' => $departmentRole,
            'workspace_role' => $workspaceRole
        ];
    }

    /**
     * Crear workspace.
     * @authenticated
     *
     * @bodyParam name string required El nombre del workspace. Example: "Nuevo Workspace"
     *
     * @response 201{}
     */
    public function store(StoreWorkspaceRequest $request)
    {
        $user = Auth::user();
        $departmentId = $request->input('department_id');
        
        if (!$user->hasDepartment($departmentId)) {
            return response()->json([
                'message' => __('messages.not_in_department')
            ], Response::HTTP_FORBIDDEN);
        }
        
        $userRole = $user->getDepartmentRole($departmentId);
        if (!in_array($userRole, ['manager', 'admin'])) {
            return response()->json([
                'message' => __('messages.only_admin_manager_create_workspace')
            ], Response::HTTP_FORBIDDEN);
        }
        
        $workspaceData = $request->validated();
        $workspaceData['owner_id'] = $user->id;

        $workspace = Workspace::create($workspaceData);

        $workspace->users()->attach($user->id, [
            'role' => 'admin',
            'joined_at' => now(),
        ]);

        $workspace->permissions = $this->getWorkspacePermissions($workspace, $user);

        return response()->json($workspace, Response::HTTP_CREATED);
    }

    /**
     * Mostrar workspace.
     * @authenticated
     *
     * @urlParam id int required El ID del workspace. Example: 1
     *
     * @response 200{}
     */
    public function show(Workspace $workspace)
    {
        $user = Auth::user();
        
        $workspace->load(['department:id,name,description', 'users:id,first_name,last_name,email']);
        
        $workspace->permissions = $this->getWorkspacePermissions($workspace, $user);
        
        return response()->json($workspace);
    }

    /**
     * Actualizar workspace.
     * @authenticated
     *
     * @urlParam id int required El ID del workspace. Example: 1
     * @bodyParam name string required El nuevo nombre del workspace. Example: "Workspace Actualizado"
     *
     * @response 200{}
     */
    public function update(UpdateWorkspaceRequest $request, $id)
    {
        $user = Auth::user();
        $workspace = Workspace::findOrFail($id);
        
        if (!$user->hasDepartment($workspace->department_id)) {
            return response()->json([
                'message' => __('messages.not_in_workspace_department')
            ], Response::HTTP_FORBIDDEN);
        }
        
        $userRole = $user->getDepartmentRole($workspace->department_id);
        if (!in_array($userRole, ['manager', 'admin'])) {
            return response()->json([
                'message' => __('messages.only_admin_manager_update_workspace')
            ], Response::HTTP_FORBIDDEN);
        }
        
        $workspace->update($request->validated());
        
        $workspace->permissions = $this->getWorkspacePermissions($workspace, $user);
        
        return response()->json($workspace);
    }

    /**
     * Eliminar workspace.
     * @authenticated
     *
     * @urlParam id int required El ID del workspace. Example: 1
     *
     * @response 204 {}
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $workspace = Workspace::findOrFail($id);
        
        if (!$user->hasDepartment($workspace->department_id)) {
            return response()->json([
                'message' => __('messages.not_in_workspace_department')
            ], Response::HTTP_FORBIDDEN);
        }
        
        $userRole = $user->getDepartmentRole($workspace->department_id);
        if (!in_array($userRole, ['manager', 'admin'])) {
            return response()->json([
                'message' => __('messages.only_admin_manager_delete_workspace')
            ], Response::HTTP_FORBIDDEN);
        }
        
        $workspace->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Verificar permisos del usuario en el workspace.
     * @authenticated
     *
     * @urlParam id int required El ID del workspace. Example: 1
     *
     * @response 200 {
     *   "can_create": true,
     *   "can_update": true,
     *   "can_delete": true,
     *   "can_manage_users": true,
     *   "can_add_users": true,
     *   "department_role": "admin",
     *   "workspace_role": "admin"
     * }
     */
    public function permissions($id)
    {
        $user = Auth::user();
        $workspace = Workspace::findOrFail($id);
        
        return response()->json($this->getWorkspacePermissions($workspace, $user));
    }
}
