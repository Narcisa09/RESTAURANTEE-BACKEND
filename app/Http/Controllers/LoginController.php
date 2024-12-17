<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    /**
     * Login del usuario.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Verificar credenciales del usuario
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Mensaje según el rol
        $rol = $user->role_id; // Cambia 'role_id' si tu columna de rol tiene otro nombre
        $mensajeRol = match ($rol) {
            1 => 'Iniciaste sesión como Administrador.',
            2 => 'Iniciaste sesión como Cliente.',
            3 => 'Iniciaste sesión como SuperAdmin.',
            default => 'Iniciaste sesión como Usuario.',
        };

        $userDetails = [
            'role_id' => $user->role_id,
            'restaurante_id' => $user->restaurante_id,
        ];

        // Respuesta con token y rol
        return response()->json([
            'message' => 'Inicio de sesión exitoso.',
            'rol_message' => $mensajeRol,
            'user' => $userDetails,
            'token' => $token,
        ], 200);
    }

    /**
     * Logout del usuario.
     */
    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'Sesión cerrada exitosamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al cerrar sesión', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtener el usuario autenticado.
     */
    public function me()
    {
        $user = Auth::user();
        return response()->json(['user' => $user], 200);
    }
}
