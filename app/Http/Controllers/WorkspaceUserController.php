<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkspaceUser;
use App\Http\Requests\StoreWorkspaceUserRequest;
use App\Http\Requests\UpdateWorkspaceUserRequest;
use App\Models\Workspace;

class WorkspaceUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
