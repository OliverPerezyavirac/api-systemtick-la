<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\WorkspaceUserController;
use App\Http\Controllers\WorkspaceInvitationController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TicketTagController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas de autenticación
Route::post('/login', [UserController::class, 'login']);
Route::apiResource('users', UserController::class);

// Rutas protegidas para usuarios
Route::middleware('auth:sanctum')->group(function () {
    
    // Rutas de logout
    Route::post('/logout', [UserController::class, 'logout']);

    // Rutas de workspaces
    Route::apiResource('workspaces', WorkspaceController::class);

    // Rutas de usuarios en workspaces
    Route::get('/workspaces/{workspace}/users', [WorkspaceUserController::class, 'index']);
    Route::post('/workspaces/{workspace}/users', [WorkspaceUserController::class, 'store']);
    Route::patch('/workspaces/{workspace}/users/{user}', [WorkspaceUserController::class, 'update']);
    Route::delete('/workspaces/{workspace}/users/{user}', [WorkspaceUserController::class, 'destroy']);

    // Rutas de invitaciones a workspaces
    Route::post('/workspaces/{workspace}/invite', [WorkspaceInvitationController::class, 'invite']);
    Route::post('/workspaces/invitations/{invitation}/accept', [WorkspaceInvitationController::class, 'accept']);
    Route::post('/workspaces/invitations/{invitation}/decline', [WorkspaceInvitationController::class, 'decline']);

    // Rutas de tickets y comentarios
    Route::get('/workspaces/{workspace}/tickets', [TicketController::class, 'index']);
    Route::post('/workspaces/{workspace}/tickets', [TicketController::class, 'store']);
    Route::get('/workspaces/{workspace}/tickets/{ticket}', [TicketController::class, 'show']);
    Route::patch('/workspaces/{workspace}/tickets/{ticket}', [TicketController::class, 'update']);
    Route::delete('/workspaces/{workspace}/tickets/{ticket}', [TicketController::class, 'destroy']);
    Route::post('/workspaces/{workspace}/tickets/{ticket}/assign', [TicketController::class, 'assign']);
    Route::post('/workspaces/{workspace}/tickets/{ticket}/status', [TicketController::class, 'changeStatus']);

    // Rutas de comentarios
    Route::get('/workspaces/{workspace}/tickets/{ticket}/comments', [CommentController::class, 'index']);
    Route::post('/workspaces/{workspace}/tickets/{ticket}/comments', [CommentController::class, 'store']);

    // Rutas de notificaciones
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    Route::get('/notifications/real-time', [NotificationController::class, 'realTimeNotifications']);

    // Rutas de tags
    Route::get('/workspaces/{workspace}/tags', [TagController::class, 'index']);
    Route::post('/workspaces/{workspace}/tags', [TagController::class, 'store']);
    Route::put('/workspaces/{workspace}/tags/{tag}', [TagController::class, 'update']);
    Route::delete('/workspaces/{workspace}/tags/{tag}', [TagController::class, 'destroy']);

    // Rutas de tags en tickets
    Route::post('/tickets/{ticket}/tags', [TicketTagController::class, 'store']);
    Route::delete('/tickets/{ticket}/tags/{tag}', [TicketTagController::class, 'destroy']);
});
