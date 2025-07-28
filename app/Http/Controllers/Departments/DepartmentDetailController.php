<?php

namespace App\Http\Controllers\Departments;

use App\Models\Departments\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class DepartmentDetailController extends Controller
{
    /**
     * Obtiene todos los detalles de un departamento incluyendo su workspace, tickets y usuarios.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = Auth::user();
        
        if (!$user->hasDepartment($id)) {
            return response()->json([
                'message' => __("messages.error_access_department")
            ], Response::HTTP_FORBIDDEN);
        }

        $department = Department::with([
            'workspace',
            'users' => function ($query) {
                $query->select('users.*')
                    ->addSelect('department_user.role as department_role')
                    ->with(['generalSettings']);
            },
            'tickets' => function ($query) {
                $query->with([
                    'assignedTo',
                    'createdBy',
                    'tags',
                    'comments' => function ($query) {
                        $query->with('user')
                            ->orderBy('created_at', 'desc')
                            ->limit(5);
                    }
                ]);
            }
        ])->findOrFail($id);

        $stats = [
            'total_tickets' => $department->tickets->count(),
            'open_tickets' => $department->tickets->where('status', 'open')->count(),
            'in_progress_tickets' => $department->tickets->where('status', 'in_progress')->count(),
            'closed_tickets' => $department->tickets->where('status', 'closed')->count(),
            'total_users' => $department->users->count(),
        ];

        $viewSettings = $user->getViewSettingForWorkspace($department->workspace_id);

        return response()->json([
            'department' => $department,
            'stats' => $stats,
            'view_settings' => $viewSettings
        ]);
    }
} 