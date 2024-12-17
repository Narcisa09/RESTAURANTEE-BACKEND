<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Cambiar el rol de un usuario
     *
     * @param  int  $usuario_id
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cambiarRol($usuario_id, Request $request)
    {
        // Validar que el rol ingresado sea un valor válido
        $request->validate([
            'role_id' => 'required|integer|exists:roles,role_id', // Se valida que el role_id exista en la tabla roles
        ]);

        // Buscar al usuario que va a cambiar de rol
        $usuario = Usuario::find($usuario_id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // Actualizar el rol del usuario
        $usuario->role_id = $request->role_id;
        $usuario->save();

        return response()->json([
            'message' => 'Rol de usuario actualizado correctamente',
            'usuario' => $usuario
        ], 200);
    }
}
