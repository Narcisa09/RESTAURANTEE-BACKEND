<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckRoleadmin
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
            if (!$usuario || $usuario->role->role_id !== 2) {
                return response()->json([
                    'message' => 'Acceso denegado: solo usuarios con rol 2 pueden acceder',
                ], 403);
            }

            return $next($request);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error de autenticación: ' . $e->getMessage()], 401);
        }
    }
}