<?php

namespace App\Http\Controllers\Tags;

use App\Models\Tags\Tag;
use App\Models\Workspaces\Workspace;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

/**
 * @group Tags
 *
 * Endpoints para gestionar tags en workspaces.
 */
class TagController extends Controller
{
    /**
     * Listar tags del workspace.
     * @authenticated
     *
     * @urlParam workspace_id int required El ID del workspace. Example: 1
     *
     * @response 200 {}
     */
    public function index(Workspace $workspace)
    {
        return response()->json($workspace->tags);
    }

    /**
     * Crear un tag.
     * @authenticated
     *
     * @urlParam workspace_id int required El ID del workspace. Example: 1
     * @bodyParam name string required El nombre del tag. Example: "Urgente"
     * @bodyParam color string required El código de color hexadecimal. Example: "#FF0000"
     *
     * @response 201 {}
     */
    public function store(Request $request, Workspace $workspace)
    {
        $this->authorize('admin', $workspace);

        $validated = $request->validate([
            'name' => 'required|string|unique:tags,name,NULL,id,workspace_id,' . $workspace->id,
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $tag = $workspace->tags()->create($validated);

        return response()->json($tag, Response::HTTP_CREATED);
    }

    /**
     * Actualizar un tag.
     * @authenticated
     *
     * @urlParam workspace_id int required El ID del workspace. Example: 1
     * @urlParam tag_id int required El ID del tag. Example: 1
     * @bodyParam name string required El nuevo nombre del tag. Example: "Muy Urgente"
     * @bodyParam color string required El nuevo código de color hexadecimal. Example: "#FF0000"
     *
     * @response 200 {}
     */
    public function update(Request $request, Workspace $workspace, Tag $tag)
    {
        $this->authorize('admin', $workspace);

        $validated = $request->validate([
            'name' => 'required|string|unique:tags,name,' . $tag->id . ',id,workspace_id,' . $workspace->id,
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $tag->update($validated);

        return response()->json($tag);
    }

    /**
     * Eliminar un tag.
     * @authenticated
     *
     * @urlParam workspace_id int required El ID del workspace. Example: 1
     * @urlParam tag_id int required El ID del tag. Example: 1
     *
     * @response 204 {}
     */
    public function destroy(Workspace $workspace, Tag $tag)
    {
        $this->authorize('admin', $workspace);

        $tag->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
