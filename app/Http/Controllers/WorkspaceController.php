<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workspace;
use App\Http\Requests\Workspace\StoreWorkspaceRequest;
use App\Http\Requests\Workspace\UpdateWorkspaceRequest;
use App\Models\WorkspaceUser;

/**
 * @group Workspaces
 *
 * Endpoints para gestionar workspaces.
 */
class WorkspaceController extends Controller
{
    /**
     * Listar workspaces.
     * @authenticated
     *
     * @queryParam page int Página de resultados. Example: 1
     * @queryParam per_page int Número de resultados por página. Example: 10
     *
     * @response 200 {}
     */
    public function index()
    {
        $workspaces = Workspace::with('owner')->get();
        return response()->json($workspaces);
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
        $workspaceData = $request->validated();
        $workspaceData['owner_id'] = auth()->id();

        $workspace = Workspace::create($workspaceData);

        // Asignar automáticamente al creador como administrador
        WorkspaceUser::create([
            'workspace_id' => $workspace->id,
            'user_id' => auth()->id(),
            'role' => 'admin',
            'joined_at' => now(),
        ]);

        return response()->json($workspace, 201);
    }

    /**
     * Mostrar workspace.
     * @authenticated
     *
     * @urlParam id int required El ID del workspace. Example: 1
     *
     * @response 200{}
     */
    public function show($id)
    {
        $workspace = Workspace::with('owner')->findOrFail($id);
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
        $workspace = Workspace::findOrFail($id);
        $workspace->update($request->validated());
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
        $workspace = Workspace::findOrFail($id);
        $workspace->delete();
        return response()->json(null, 204);
    }
}
