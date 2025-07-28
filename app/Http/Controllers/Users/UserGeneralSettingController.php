<?php

namespace App\Http\Controllers\Users;

use App\Models\Users\UserGeneralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class UserGeneralSettingController extends Controller
{
    /**
     * Obtener la configuración general del usuario.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        $user = Auth::user();
        $settings = $user->generalSettings;
        
        if (!$settings) {
            $settings = UserGeneralSetting::create([
                'user_id' => $user->id,
                'sidebar_expanded' => true,
                'additional_settings' => []
            ]);
        }

        return response()->json($settings);
    }

    /**
     * Actualizar la configuración general del usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'sidebar_expanded' => 'boolean',
            'additional_settings' => 'nullable|array'
        ]);

        $settings = $user->generalSettings;
        
        if ($settings) {
            $settings->update($request->only(['sidebar_expanded', 'additional_settings']));
        } else {
            $settings = UserGeneralSetting::create([
                'user_id' => $user->id,
                'sidebar_expanded' => $request->sidebar_expanded ?? true,
                'additional_settings' => $request->additional_settings ?? []
            ]);
        }

        return response()->json($settings);
    }
} 