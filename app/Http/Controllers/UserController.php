<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use Illuminate\Support\Facades\Auth;

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
        $users = User::with('relationships')->paginate(10);
        return response()->json($users);
    }

    public function create()
    {
        //
    }

    /**
     * Crear usuario.
     *
     * @param  \Illuminate\Http\Request  $request
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
        $user = User::with('relationships')->findOrFail($id);
        return response()->json($user);
    }

    public function edit($id)
    {
        //
    }

    /**
     * Actualizar usuario.
     * @authenticated
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
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

        return response()->json(['message' => 'Credenciales inválidas'], 401);
    }

    /**
     * Cerrar sesión.
     * @authenticated
     * @response 204
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
