<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Workspace;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of the tags.
     *
     * @param  \App\Models\Workspace  $workspace
     * @return \Illuminate\Http\Response
     */
    public function index(Workspace $workspace)
    {
        return response()->json($workspace->tags);
    }

    /**
     * Store a new tag.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Workspace  $workspace
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Workspace $workspace)
    {
        $this->authorize('admin', $workspace);

        $validated = $request->validate([
            'name' => 'required|string|unique:tags,name,NULL,id,workspace_id,' . $workspace->id,
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $tag = $workspace->tags()->create($validated);

        return response()->json($tag, 201);
    }

    /**
     * Update a tag.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Workspace  $workspace
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
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
     * Remove a tag from a workspace.
     *
     * @param  \App\Models\Workspace  $workspace
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function destroy(Workspace $workspace, Tag $tag)
    {
        $this->authorize('admin', $workspace);

        $tag->delete();

        return response()->json(null, 204);
    }
}
