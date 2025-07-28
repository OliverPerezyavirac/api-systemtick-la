<?php

namespace App\Http\Controllers\Users;

use App\Models\Users\UserViewSetting;
use App\Models\Workspaces\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class UserViewSettingController extends Controller
{
    /**
     * Obtener la configuración de vista del usuario para un workspace específico.
     *
     * @param  int  $workspaceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($workspaceId)
    {
        $user = Auth::user();
        
        if (!$user->canAccessWorkspace($workspaceId)) {
            return response()->json([
                'message' => __('messages.error_access_workspace')
            ], Response::HTTP_FORBIDDEN);
        }

        $settings = $user->getViewSettingForWorkspace($workspaceId);
        
        if (!$settings) {
            $settings = UserViewSetting::create([
                'user_id' => $user->id,
                'workspace_id' => $workspaceId,
                'view_type' => 'list',
                'additional_settings' => []
            ]);
        }

        return response()->json($settings);
    }

    /**
     * Actualizar la configuración de vista del usuario para un workspace específico.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $workspaceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $workspaceId)
    {
        $user = Auth::user();
        
        if (!$user->canAccessWorkspace($workspaceId)) {
            return response()->json([
                'message' => __('messages.error_access_workspace')
            ], Response::HTTP_FORBIDDEN);
        }

        $request->validate([
            'view_type' => 'required|in:list,kanban',
            'additional_settings' => 'nullable|array'
        ]);

        $settings = $user->getViewSettingForWorkspace($workspaceId);
        
        if ($settings) {
            $settings->update($request->only(['view_type', 'additional_settings']));
        } else {
            $settings = UserViewSetting::create([
                'user_id' => $user->id,
                'workspace_id' => $workspaceId,
                'view_type' => $request->view_type,
                'additional_settings' => $request->additional_settings ?? []
            ]);
        }

        return response()->json($settings);
    }

    /**
     * Eliminar la configuración de vista del usuario para un workspace específico.
     *
     * @param  int  $workspaceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($workspaceId)
    {
        $user = Auth::user();
        
        if (!$user->canAccessWorkspace($workspaceId)) {
            return response()->json([
                'message' => __('messages.error_access_workspace')
            ], Response::HTTP_FORBIDDEN);
        }

        $settings = $user->getViewSettingForWorkspace($workspaceId);
        
        if ($settings) {
            $settings->delete();
            return response()->json([
                'message' => __('messages.view_setting_deleted')
            ]);
        }

        return response()->json([
            'message' => __('messages.view_setting_not_found')
        ], Response::HTTP_NOT_FOUND);
    }
} 
