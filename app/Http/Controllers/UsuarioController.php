<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Middleware\CheckRole;

class UsuarioController extends Controller
{
    /**
     * Registro de sesión del usuario (rol = 1: cliente).
     */
    public function register(Request $request)
    {
        // Validación de los datos de entrada
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:50|unique:usuarios',
            'email' => 'required|string|email|max:100|unique:usuarios',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Crear el nuevo usuario con rol 1 (cliente)
        $usuario = Usuario::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 1, // Rol de cliente
        ]);

        // Generar el token JWT
        $token = JWTAuth::fromUser($usuario);

        return response()->json([
            'usuario' => $usuario,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Iniciar sesión del usuario (rol = 1: cliente).
     */
    public function login(Request $request)
    {
        // Validación de los datos de entrada
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Intentar autenticar con las credenciales
        $credentials = $request->only(['email', 'password']);
        
        try {
            // Verificar si las credenciales son correctas
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['message' => 'Credenciales inválidas'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al crear el token'], 500);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Verificar si el usuario está autenticado y su rol es el correcto (cliente).
     */
    public function me(Request $request)
    {
        // Obtener el usuario autenticado
        try {
            $usuario = JWTAuth::parseToken()->authenticate();

            // Verificar que el rol sea cliente (role_id = 1)
            if ($usuario->role->role_id !== 1) {
                return response()->json(['message' => 'Acceso denegado: solo el cliente puede acceder a esta información'], 403);
            }

            return response()->json([
                'usuario' => $usuario,
                'role' => $usuario->role->role_name,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'No se pudo obtener el usuario'], 401);
        }
    }
}
