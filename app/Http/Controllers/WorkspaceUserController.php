<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkspaceUser;
use App\Http\Requests\StoreWorkspaceUserRequest;
use App\Http\Requests\UpdateWorkspaceUserRequest;
use App\Models\Workspace;

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
        $workspace = Workspace::find($workspaceId);

        if (!$workspace) {
            return response()->json(['message' => 'Workspace not found'], 404);
        }

        $users = WorkspaceUser::where('workspace_id', $workspaceId)->with('user')->get();
        return response()->json($users);
    }

    /**
     * Agregar usuario al workspace.
     * @authenticated
     *
     * @urlParam workspace_id int required El ID del workspace. Example: 1
     * @bodyParam user_id int required El ID del usuario a agregar. Example: 2
     * @bodyParam role string required El rol del usuario en el workspace. Example: "member"
     *
     * @response 201{}
     */
    public function store(StoreWorkspaceUserRequest $request, $workspaceId)
    {
        $data = $request->validated();
        $data['workspace_id'] = $workspaceId;

        $workspaceUser = WorkspaceUser::firstOrCreate(
            ['workspace_id' => $workspaceId, 'user_id' => $data['user_id']],
            $data
        );

        return response()->json($workspaceUser, 201);
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
    public function update(UpdateWorkspaceUserRequest $request, $workspaceId, $userId)
    {
        $workspaceUser = WorkspaceUser::where('workspace_id', $workspaceId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $workspaceUser->update($request->validated());

        return response()->json($workspaceUser);
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
        $workspaceUser = WorkspaceUser::where('workspace_id', $workspaceId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $workspaceUser->delete();

        return response()->json(null, 204);
    }
}
