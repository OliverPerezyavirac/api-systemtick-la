<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications.
     *
     * @return \Illuminate\Http\Response
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
     * Mark a notification as read.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        
        if ($notification->read_at !== null) {
            return response()->json(['message' => 'Notification already read'], 400);
        }
        
        $notification->markAsRead();
        return response()->json(['message' => 'Notification marked as read']);
    }

    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        return response()->json([
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Delete a notification.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
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
