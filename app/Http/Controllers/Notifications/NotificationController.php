<?php

namespace App\Http\Controllers\Notifications;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

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
            return response()->json([
                'message' => __('messages.notification_already_read')
            ], Response::HTTP_BAD_REQUEST);
        }
        
        $notification->markAsRead();
        return response()->json([
            'message' => __('messages.notification_marked_as_read')
        ]);
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
        
        return response()->json([
            'message' => __('messages.notification_deleted')
        ]);
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
