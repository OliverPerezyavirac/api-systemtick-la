<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Models\Users\User;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

/**
 * @group Usuarios
 *
 * Endpoints para gestionar usuarios.
 */
class UserController extends Controller
{
    /**
     * Listar usuarios.
     * @authenticated
     *
     * @queryParam page int Página de resultados. Example: 1
     * @queryParam per_page int Número de resultados por página. Example: 10
     *
     * @response 200 
     */
    public function index()
    {
        $users = User::with(['departments', 'workspaces'])->paginate(10);
        return response()->json($users);
    }

    public function create()
    {
        //
    }

    /**
     * Crear usuario.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());
        return response()->json($user, 201);
    }

    /**
     * Mostrar usuario.
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $user = User::with([
                'departments' => function($query) {
                    $query->select('departments.id', 'departments.name', 'departments.description')
                          ->withPivot('role');
                },
                'workspaces' => function($query) {
                    $query->select('workspaces.id', 'workspaces.name', 'workspaces.description')
                          ->withPivot('role');
                }
            ])->findOrFail($id);

            if (Auth::id() === $user->id) {
                return response()->json([
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'avatar_url' => $user->avatar_url,
                    'is_active' => $user->is_active,
                    'email_verified_at' => $user->email_verified_at,
                    'departments' => $user->departments,
                    'workspaces' => $user->workspaces,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at
                ]);
            }

            return response()->json([
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'avatar_url' => $user->avatar_url,
                'departments' => $user->departments,
                'workspaces' => $user->workspaces
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => __('messages.user_not_found')
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('messages.error_fetching_user')
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function edit($id)
    {
        //
    }

    /**
     * Actualizar usuario.
     * @authenticated
     *
     * @param  \Illuminate\Http\Request
     * @param  int
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->validated());
        return response()->json($user);
    }

    /**
     * Eliminar usuario.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(null, 204);
    }

    /**
     * Buscar usuario por email.
     * @authenticated
     *
     * @urlParam email string required El correo electrónico del usuario. Example: john@example.com
     *
     * @response 200 {}
     */
    public function findByEmail($email)
    {
        $user = User::where('email', $email)->firstOrFail();
        return response()->json($user);
    }

    /**
     * Iniciar sesión.
     *
     * @bodyParam email string required El correo electrónico del usuario. Example: john@example.com
     * @bodyParam password string required La contraseña del usuario. Example: secret
     *
     * @response 200
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'avatar_url' => $user->avatar_url,
                ],
            ]);
        }

        return response()->json(['message' => __('messages.invalid_credentials')], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Cerrar sesión.
     * @authenticated
     * @response 204
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => __('messages.logout_success')], Response::HTTP_OK);
    }

    /**
     * Obtener perfil del usuario autenticado.
     * @authenticated
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        try {
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'message' => __('messages.unauthenticated')
                ], Response::HTTP_UNAUTHORIZED);
            }

            $user->load([
                'departments' => function($query) {
                    $query->select('departments.id', 'departments.name', 'departments.description')
                          ->withPivot('role');
                },
                'workspaces' => function($query) {
                    $query->select('workspaces.id', 'workspaces.name', 'workspaces.description')
                          ->withPivot('role');
                }
            ]);

            return response()->json([
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'avatar_url' => $user->avatar_url,
                'is_active' => $user->is_active,
                'email_verified_at' => $user->email_verified_at,
                'departments' => $user->departments,
                'workspaces' => $user->workspaces,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('messages.error_fetching_profile')
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
