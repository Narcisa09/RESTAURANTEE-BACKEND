<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Mesa;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ReservaController extends Controller
{
    public function index()
    {
        // Obtener el usuario autenticado
        $usuario = JWTAuth::parseToken()->authenticate();

        // Verificar que el usuario sea administrador
        if ($usuario->role_id !== 2) {
            return response()->json(['error' => 'Acceso no autorizado'], 403);
        }

        // Obtener las reservas asociadas al restaurante del administrador
        $reservas = Reserva::whereHas('mesa', function ($query) use ($usuario) {
            $query->where('restaurante_id', $usuario->restaurante_id);
        })->with(['usuario', 'mesa'])->get();

        return response()->json($reservas);
    }

    public function show($reserva_id)
    {
        // Obtener el usuario autenticado
        $usuario = JWTAuth::parseToken()->authenticate();

        // Verificar que el usuario sea administrador
        if ($usuario->role_id !== 2) {
            return response()->json(['error' => 'Acceso no autorizado'], 403);
        }

        // Buscar la reserva y verificar que pertenece al restaurante del administrador
        $reserva = Reserva::where('reserva_id', $reserva_id)
            ->whereHas('mesa', function ($query) use ($usuario) {
                $query->where('restaurante_id', $usuario->restaurante_id);
            })
            ->with(['usuario', 'mesa'])
            ->first();

        if (!$reserva) {
            return response()->json(['error' => 'Reserva no encontrada o no pertenece a su restaurante'], 404);
        }

        return response()->json($reserva);
    }

    /**
     * Crear una nueva reserva.
     */
    public function store(Request $request)
    {
        // $usuario = JWTAuth::parseToken()->authenticate();

        // Validar los datos de la solicitud
        $validatedData = $request->validate([
            'mesa_id' => 'required|exists:mesa,mesa_id',
            'fecha_reserva' => 'required|date|after_or_equal:today',
        ]);

        // Verificar si la mesa pertenece al restaurante seleccionado
        $mesa = Mesa::findOrFail($request->mesa_id);

        // Verificar si la mesa ya está reservada en la fecha indicada
        $reservaExistente = Reserva::where('mesa_id', $mesa->mesa_id)
            ->where('fecha_reserva', $request->fecha_reserva)
            ->first();

        if ($reservaExistente) {
            return response()->json(['message' => 'La mesa ya está reservada para esta fecha.'], 400);
        }

        // Crear la reserva
        $reserva = new Reserva();
        $reserva->usuario_id = $usuario->usuario_id;
        $reserva->mesa_id = $mesa->mesa_id;
        $reserva->fecha_reserva = $request->fecha_reserva;
        $reserva->estado = 'pendiente'; // Puedes usar un estado inicial
        $reserva->save();

        $mesa->disponibilidad = false; // Marcar la mesa como no disponible
        $mesa->save();
        return response()->json(['message' => 'Reserva realizada con éxito', 'reserva' => $reserva], 201);
    }
   
  
    public function update(Request $request, $reserva_id)
    {
        // Obtener el usuario autenticado
        $usuario = JWTAuth::parseToken()->authenticate();

        // Verificar que el usuario sea administrador
        if ($usuario->role_id !== 2) {
            return response()->json(['error' => 'Acceso no autorizado'], 403);
        }

        $reserva = Reserva::where('reserva_id', $reserva_id)
            ->whereHas('mesa', function ($query) use ($usuario) {
                $query->where('restaurante_id', $usuario->restaurante_id);
            })
            ->first();

        if (!$reserva) {
            return response()->json(['error' => 'Reserva no encontrada o no pertenece a su restaurante'], 404);
        }

        $request->validate([
            'estado' => 'required|string',
        ]);

        $reserva->update($request->all());

        return response()->json(['message' => 'Reserva actualizada con éxito', 'reserva' => $reserva]);
    }

    public function destroy($reserva_id)
    {
        // Obtener el usuario autenticado
        $usuario = JWTAuth::parseToken()->authenticate();

        // Verificar que el usuario sea administrador
        if ($usuario->role_id !== 2) {
            return response()->json(['error' => 'Acceso no autorizado'], 403);
        }

        $reserva = Reserva::where('reserva_id', $reserva_id)
            ->whereHas('mesa', function ($query) use ($usuario) {
                $query->where('restaurante_id', $usuario->restaurante_id);
            })
            ->first();

        if (!$reserva) {
            return response()->json(['error' => 'Reserva no encontrada o no pertenece a su restaurante'], 404);
        }

        $reserva->delete();

        return response()->json(['message' => 'Reserva eliminada con éxito']);
    }


    public function cambiarEstado($reserva_id, Request $request)
    {
        $usuario = JWTAuth::parseToken()->authenticate();

        // Verificar que el usuario sea administrador
        if ($usuario->role_id !== 2) {
            return response()->json(['error' => 'Acceso no autorizado'], 403);
        }

        $reserva = Reserva::where('reserva_id', $reserva_id)
            ->whereHas('mesa', function ($query) use ($usuario) {
                $query->where('restaurante_id', $usuario->restaurante_id);
            })
            ->first();

        if (!$reserva) {
            return response()->json(['error' => 'Reserva no encontrada o no pertenece a su restaurante'], 404);
        }

        $request->validate([
            'estado' => 'required|string|max:255',
        ]);

        $reserva->estado = $request->estado;
        $reserva->save();

        return response()->json(['message' => 'Estado de la reserva actualizado con éxito']);
    }

}

