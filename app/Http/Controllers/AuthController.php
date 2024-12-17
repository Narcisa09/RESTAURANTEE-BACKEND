<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth; 
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:50|unique:usuarios',
            'email' => 'required|string|email|max:100|unique:usuarios',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $usuario = Usuario::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 3,
        ]);

        // Generar el token JWT
        $token = JWTAuth::fromUser($usuario);

        return response()->json([
            'usuario' => $usuario,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $credentials = $request->only(['email', 'password']);

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['message' => 'Credenciales inválidas'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['message' => 'No se pudo crear el token'], 500);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'Sesión cerrada con éxito']);
        } catch (JWTException $e) {
            return response()->json(['message' => 'No se pudo cerrar la sesión'], 500);
        }
    }

    public function me()
    {
        try {
            $usuario = JWTAuth::parseToken()->authenticate();

            if (!$usuario) {
                return response()->json(['message' => 'Usuario no encontrado'], 404);
            }

            return response()->json(['usuario' => $usuario]);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Token inválido o expirado'], 401);
        }
    }


    public function mesasPorRestaurante($restaurante_id)
{
    // Obtener el usuario autenticado
    $usuario = JWTAuth::parseToken()->authenticate();

    // Verificar que el usuario tenga el rol 1 (cliente)
    if ($usuario->role_id != 1) {
        return response()->json(['error' => 'No tienes permisos para acceder a esta información.'], 403);
    }

    // Buscar las mesas del restaurante especificado
    $mesas = Mesa::where('restaurante_id', $restaurante_id)
        ->get();

    // Si no hay mesas para el restaurante, devolver un mensaje
    if ($mesas->isEmpty()) {
        return response()->json(['message' => 'No hay mesas disponibles para este restaurante.'], 404);
    }

    // Devolver las mesas asociadas al restaurante
    return response()->json($mesas, 200);
}
}
