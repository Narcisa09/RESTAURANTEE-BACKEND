<?php

namespace App\Http\Controllers;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminController extends Controller
{
    /**
     * Obtener los usuarios que hicieron reservas en el restaurante asociado al administrador.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerUsuariosConReservas()
    {
        try {
            // Obtener el usuario autenticado
            $admin = JWTAuth::parseToken()->authenticate();

            // Verificar si el usuario tiene el rol 2 (admin)
            if ($admin->role_id !== 2) {
                return response()->json(['error' => 'Acceso denegado: solo administradores pueden realizar esta acción.'], 403);
            }

            // Verificar que el administrador tiene un restaurante asociado
            if (is_null($admin->restaurante_id)) {
                return response()->json(['error' => 'El administrador no tiene un restaurante asociado.'], 400);
            }

            // Obtener los usuarios con reservas en el restaurante del administrador
            $usuariosConReservas = Usuario::whereHas('reservas', function ($query) use ($admin) {
                $query->whereHas('mesa', function ($subQuery) use ($admin) {
                    $subQuery->where('restaurante_id', $admin->restaurante_id);
                });
            })
            ->with(['reservas' => function ($query) use ($admin) {
                $query->whereHas('mesa', function ($subQuery) use ($admin) {
                    $subQuery->where('restaurante_id', $admin->restaurante_id);
                })->with('mesa');
            }])->get();

            return response()->json(['usuarios' => $usuariosConReservas], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener usuarios con reservas: ' . $e->getMessage()], 500);
        }
    }

}

