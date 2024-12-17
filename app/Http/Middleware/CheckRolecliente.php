<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckRolecliente
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // Obtener el usuario autenticado
            $usuario = JWTAuth::parseToken()->authenticate();

            // Verificar si el usuario tiene el rol 1 (cliente)
            if ($usuario->role_id !== 1) {
                return response()->json(['message' => 'Acceso denegado: solo usuarios con rol de cliente pueden acceder'], 403);
            }

            return $next($request);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error de autenticación: ' . $e->getMessage()], 401);
        }
    }
}
