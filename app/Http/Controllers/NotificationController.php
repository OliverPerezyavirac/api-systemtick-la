<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Notificaciones
 *
 * Endpoints para gestionar notificaciones de usuarios.
 */
class NotificationController extends Controller
{
    /**
     * Listar notificaciones del usuario.
     * @authenticated
     *
     * @response 200 {}
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->latest()
            ->paginate(20);
        
        return response()->json([
            'notifications' => $notifications->items(),
            'total' => $notifications->total()
        ]);
    }

    /**
     * Marcar notificación como leída.
     * @authenticated
     *
     * @urlParam notification_id int required El ID de la notificación. Example: 1
     *
     * @response 200{}
     */
    public function markAsRead($notification_id)
    {
        $notification = Auth::user()->notifications()->findOrFail($notification_id);
        
        if ($notification->read_at !== null) {
            return response()->json(['message' => 'Notification already read'], 400);
        }
        
        $notification->markAsRead();
        return response()->json(['message' => 'Notification marked as read']);
    }

    /**
     * Eliminar notificación.
     * @authenticated
     *
     * @urlParam notification_id int required El ID de la notificación. Example: 1
     *
     * @response 204 {}
     */
    public function destroy($notification_id)
    {
        $notification = Auth::user()->notifications()->findOrFail($notification_id);
        $notification->delete();
        
        return response()->json(['message' => 'Notification deleted']);
    }

    /**
     * Get real-time notifications for the current user.
     *
     * @return \Illuminate\Http\Response
     */
    public function realTimeNotifications()
    {
        $user = Auth::user();
        $notifications = $user->unreadNotifications()
            ->latest()
            ->take(10)
            ->get();
        
        return response()->json([
            'notifications' => $notifications,
            'count' => $notifications->count()
        ]);
    }
}
