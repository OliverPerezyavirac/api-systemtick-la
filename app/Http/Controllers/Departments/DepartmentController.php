<?php

namespace App\Http\Controllers\Departments;

use App\Models\Departments\Department;
use App\Models\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class DepartmentController extends Controller
{
    /**
     * Listar departamentos del usuario.
     * @authenticated
     *
     * @queryParam page int Página de resultados. Example: 1
     * @queryParam per_page int Número de resultados por página. Example: 10
     *
     * @response 200 {}
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->input('per_page', 10);
        
        $departments = $user->departments()
            ->with('workspaces')
            ->paginate($perPage);
            

        $departments->getCollection()->transform(function($department) use ($user) {
            $department->user_role = $user->getDepartmentRole($department->id);
            return $department;
        });

        return response()->json($departments);
    }

    /**
     * Crear departamento.
     * @authenticated
     *
     * @bodyParam name string required El nombre del departamento. Example: "Departamento de IT"
     * @bodyParam description string La descripción del departamento. Example: "Departamento de Tecnologías de la Información"
     * @bodyParam logo_url string La URL del logo del departamento. Example: "https://example.com/logo.png"
     * @bodyParam status string El estado del departamento. Example: "active"
     *
     * @response 201 {}
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo_url' => 'nullable|string|url',
            'status' => 'sometimes|in:active,inactive'
        ]);

        $department = Department::create($request->all());

        $department->users()->attach(Auth::id(), ['role' => 'admin']);

        return response()->json($department, Response::HTTP_CREATED);
    }

    /**
     * Mostrar departamento.
     * @authenticated
     *
     * @urlParam id int required El ID del departamento. Example: 1
     *
     * @response 200 {}
     */
    public function show($id)
    {
        $department = Department::with(['users' => function($query) {
            $query->select('users.id', 'users.first_name', 'users.last_name', 'users.email');
        }])->findOrFail($id);
        
        if (!Auth::user()->hasDepartment($id)) {
            return response()->json([
                'message' => __("messages.error_access_department")
            ], Response::HTTP_FORBIDDEN);
        }

        $users = $department->users->map(function($user) {
            return [
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email
                ],
                'role' => $user->pivot->role
            ];
        });

        return response()->json([
            'id' => $department->id,
            'name' => $department->name,
            'description' => $department->description,
            'logo_url' => $department->logo_url,
            'status' => $department->status,
            'users' => $users
        ]);
    }

    /**
     * Actualizar departamento.
     * @authenticated
     *
     * @urlParam id int required El ID del departamento. Example: 1
     * @bodyParam name string El nuevo nombre del departamento. Example: "Departamento de IT Actualizado"
     * @bodyParam description string La nueva descripción del departamento.
     * @bodyParam logo_url string La nueva URL del logo del departamento. Example: "https://example.com/new_logo.png"
     * @bodyParam status string El nuevo estado del departamento. Example: "active"
     *
     * @response 200 {}
     */
    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);
        
        if (!Auth::user()->hasDepartment($id)) {
            return response()->json([
                'message' => __("messages.error_access_department")
            ], Response::HTTP_FORBIDDEN);
        }

        if (Auth::user()->getDepartmentRole($id) !== 'admin') {
            return response()->json([
                'message' => __("messages.only_admin_update_department")
            ], Response::HTTP_FORBIDDEN);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'logo_url' => 'nullable|string|url',
            'status' => 'sometimes|in:active,inactive'
        ]);

        $department->update($request->all());
        return response()->json($department);
    }

    /**
     * Eliminar departamento.
     * @authenticated
     *
     * @urlParam id int required El ID del departamento. Example: 1
     *
     * @response 204 {}
     */
    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        
        if (!Auth::user()->hasDepartment($id)) {
            return response()->json([
                'message' => __("messages.error_access_department")
            ], Response::HTTP_FORBIDDEN);
        }

        if (Auth::user()->getDepartmentRole($id) !== 'admin') {
            return response()->json([
                'message' => __("messages.only_admin_delete_department")
            ], Response::HTTP_FORBIDDEN);
        }

        $department->delete();
        return response()->json(null, 204);
    }

    /**
     * Agregar usuario al departamento.
     * @authenticated
     *
     * @urlParam id int required El ID del departamento. Example: 1
     * @bodyParam user_id int required El ID del usuario a agregar. Example: 2
     * @bodyParam role string required El rol del usuario en el departamento. Example: "member"
     *
     * @response 200 {}
     */
    public function addUser(Request $request, $id)
    {
        $department = Department::findOrFail($id);
        
        if (!Auth::user()->hasDepartment($id)) {
            return response()->json([
                'message' => __("messages.error_access_department")
            ], Response::HTTP_FORBIDDEN);
        }

        if (Auth::user()->getDepartmentRole($id) !== 'admin') {
            return response()->json([
                'message' => __("messages.only_admin_add_user")
            ], Response::HTTP_FORBIDDEN);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:admin,manager,member'
        ]);

        if ($department->hasUser($request->user_id)) {
            return response()->json([
                'message' => __("messages.user_already_in_department")
            ], Response::HTTP_CONFLICT);
        }

        $department->users()->attach($request->user_id, ['role' => $request->role]);
        return response()->json([
            'message' => __("messages.user_added_to_department")
        ]);
    }

    /**
     * Eliminar usuario del departamento.
     * @authenticated
     *
     * @urlParam id int required El ID del departamento. Example: 1
     * @urlParam user_id int required El ID del usuario a eliminar. Example: 2
     *
     * @response 200 {}
     */
    public function removeUser($id, $userId)
    {
        $department = Department::findOrFail($id);
        
        if (!Auth::user()->hasDepartment($id)) {
            return response()->json([
                'message' => __("messages.error_access_department")
            ], Response::HTTP_FORBIDDEN);
        }

        if (Auth::user()->getDepartmentRole($id) !== 'admin') {
            return response()->json([
                'message' => __("messages.only_admin_remove_user")
            ], Response::HTTP_FORBIDDEN);
        }

        if ($department->users()->where('role', 'admin')->count() <= 1 && 
            $department->getUserRole($userId) === 'admin') {
            return response()->json([
                'message' => __("messages.cannot_remove_last_admin")
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $department->users()->detach($userId);
        return response()->json([
            'message' => __("messages.user_removed_from_department")
        ]);
    }

    /**
     * Buscar departamentos.
     * @authenticated
     *
     * @queryParam search string Término de búsqueda. Example: "IT"
     * @queryParam page int Página de resultados. Example: 1
     * @queryParam per_page int Número de resultados por página. Example: 10
     *
     * @response 200 {}
     */
    public function search(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search', '');
        $perPage = $request->input('per_page', 10);
        
        $departments = $user->departments()
            ->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->with('workspaces')
            ->paginate($perPage);
            
        $departments->getCollection()->transform(function($department) use ($user) {
            $department->user_role = $user->getDepartmentRole($department->id);
            return $department;
        });

        return response()->json($departments);
    }
} 