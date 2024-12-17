<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Obtener el usuario autenticado
            $usuario = JWTAuth::parseToken()->authenticate();

            // Verificar si el usuario tiene el rol 3
            if (!$usuario || $usuario->role->role_id !== 3) {
                return response()->json([
                    'message' => 'Acceso denegado: solo usuarios con rol 3 pueden acceder',
                ], 403);
            }

            return $next($request);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error de autenticación: ' . $e->getMessage()], 401);
        }
    }
}
