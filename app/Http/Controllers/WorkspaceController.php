<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workspace;
use App\Http\Requests\Workspace\StoreWorkspaceRequest;
use App\Http\Requests\Workspace\UpdateWorkspaceRequest;
use App\Models\WorkspaceUser;

class WorkspaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $workspaces = Workspace::with('owner')->get();
        return response()->json($workspaces);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $workspace = Workspace::with('owner')->findOrFail($id);
        return response()->json($workspace);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateWorkspaceRequest $request, $id)
    {
        $workspace = Workspace::findOrFail($id);
        $workspace->update($request->validated());
        return response()->json($workspace);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $workspace = Workspace::findOrFail($id);
        $workspace->delete();
        return response()->json(null, 204);
    }
}
