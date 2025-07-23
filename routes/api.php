<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Workspaces\WorkspaceController;
use App\Http\Controllers\WorkspacesUsers\WorkspaceUserController;
use App\Http\Controllers\Workspaces\WorkspaceInvitationController;
use App\Http\Controllers\Tickets\TicketController;
use App\Http\Controllers\Comments\CommentController;
use App\Http\Controllers\Notifications\NotificationController;
use App\Http\Controllers\Tags\TagController;
use App\Http\Controllers\Tickets\TicketTagController;
use App\Http\Controllers\Users\UserViewSettingController;
use App\Http\Controllers\Departments\DepartmentController;
use App\Http\Controllers\Users\UserGeneralSettingController;
use App\Http\Controllers\Departments\DepartmentDetailController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas de autenticación
Route::post('/login', [UserController::class, 'login']);
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/email/{email}', [UserController::class, 'findByEmail']);

// Rutas protegidas para usuarios
Route::middleware('auth:sanctum')->group(function () {
    
    // Rutas de logout
    Route::post('/logout', [UserController::class, 'logout']);

    // Rutas de workspaces
    Route::apiResource('workspaces', WorkspaceController::class);
    Route::get('/workspaces/{id}/permissions', [WorkspaceController::class, 'permissions']);

    // Rutas de departamentos
    Route::get('/departments/search', [DepartmentController::class, 'search']);
    Route::apiResource('departments', DepartmentController::class);
    Route::post('/departments/{id}/users', [DepartmentController::class, 'addUser']);
    Route::delete('/departments/{id}/users/{userId}', [DepartmentController::class, 'removeUser']);
    Route::get('/departments/{id}/details', [DepartmentDetailController::class, 'show']);

    // Rutas de usuarios en workspaces
    Route::get('/workspaces/{workspace}/users', [WorkspaceUserController::class, 'index']);
    Route::post('/workspaces/{workspace}/users', [WorkspaceUserController::class, 'store']);
    Route::patch('/workspaces/{workspace}/users/{user}', [WorkspaceUserController::class, 'update']);
    Route::delete('/workspaces/{workspace}/users/{user}', [WorkspaceUserController::class, 'destroy']);

    // Rutas de invitaciones a workspaces
    Route::post('/workspaces/{workspace}/invite', [WorkspaceInvitationController::class, 'invite']);
    Route::post('/workspaces/invitations/{invitation}/accept', [WorkspaceInvitationController::class, 'accept']);
    Route::post('/workspaces/invitations/{invitation}/decline', [WorkspaceInvitationController::class, 'decline']);

    // Rutas de tickets
    Route::get('/workspaces/{workspace}/tickets', [TicketController::class, 'index']);
    Route::post('/workspaces/{workspace}/tickets', [TicketController::class, 'store']);
    Route::get('/workspaces/{workspace}/tickets/{ticket}', [TicketController::class, 'show']);
    Route::patch('/workspaces/{workspace}/tickets/{ticket}', [TicketController::class, 'update']);
    Route::delete('/workspaces/{workspace}/tickets/{ticket}', [TicketController::class, 'destroy']);
    Route::post('/workspaces/{workspace}/tickets/{ticket}/assign', [TicketController::class, 'assign']);
    Route::post('/workspaces/{workspace}/tickets/{ticket}/status', [TicketController::class, 'changeStatus']);
    Route::get('/workspaces/{workspaceId}/tickets/{ticketId}/ai-suggestions', [TicketController::class, 'getAISuggestions']);

    // Rutas de comentarios
    Route::get('/workspaces/{workspace}/tickets/{ticket}/comments', [CommentController::class, 'index']);
    Route::post('/workspaces/{workspace}/tickets/{ticket}/comments', [CommentController::class, 'store']);

    // Rutas de notificaciones
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    Route::get('/notifications/real-time', [NotificationController::class, 'realTimeNotifications']);

    // Rutas de configuraciones de vista
    Route::get('/workspaces/{workspace}/view-settings', [UserViewSettingController::class, 'show']);
    Route::put('/workspaces/{workspace}/view-settings', [UserViewSettingController::class, 'update']);
    Route::delete('/workspaces/{workspace}/view-settings', [UserViewSettingController::class, 'destroy']);

    // Rutas de tags
    Route::get('/workspaces/{workspace}/tags', [TagController::class, 'index']);
    Route::post('/workspaces/{workspace}/tags', [TagController::class, 'store']);
    Route::put('/workspaces/{workspace}/tags/{tag}', [TagController::class, 'update']);
    Route::delete('/workspaces/{workspace}/tags/{tag}', [TagController::class, 'destroy']);

    // Rutas de tags en tickets
    Route::post('/tickets/{ticket}/tags', [TicketTagController::class, 'store']);
    Route::delete('/tickets/{ticket}/tags/{tag}', [TicketTagController::class, 'destroy']);

    // Rutas de usuario protegidas
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::get('/profile', [UserController::class, 'profile']);

    // Rutas para configuraciones generales del usuario
    Route::get('/user/general-settings', [UserGeneralSettingController::class, 'show']);
    Route::put('/user/general-settings', [UserGeneralSettingController::class, 'update']);
});
