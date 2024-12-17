<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class MesaController extends Controller
{
    /**
     * Mostrar todas las mesas de un restaurante.
     */
    public function index()
    {
        // Obtener el usuario autenticado
        $usuario = JWTAuth::parseToken()->authenticate();

        if (!$usuario->restaurante_id) {
            return response()->json(['error' => 'El usuario no tiene un restaurante asociado.'], 403);
        }
        
        $mesas = Mesa::where('restaurante_id', $usuario->restaurante_id)->get();

        return response()->json($mesas, 200);
    }

    /**
     * Crear una nueva mesa.
     */
    public function store(Request $request)
    {
        // Obtener el usuario autenticado
        $usuario = JWTAuth::parseToken()->authenticate();

        if (!$usuario->restaurante_id) {
            return response()->json(['error' => 'El usuario no tiene un restaurante asociado.'], 403);
        }

        $request->validate([
            'numero_mesa' => 'required|integer',
            'capacidad' => 'required|integer|min:1',

            'disponibilidad' => 'required|boolean',

        ]);
    
        $mesa = Mesa::create([
            'numero_mesa' => $request->numero_mesa,
            'capacidad' => $request->capacidad,
            'disponibilidad' => $request->disponibilidad,
            'restaurante_id' => $usuario->restaurante_id,
        ]);
    
        return response()->json([
            'message' => 'Mesa creada exitosamente',
            'mesa' => $mesa
        ], 201);
    }

    /**
     * Mostrar una mesa específica.
     */
    public function show($mesa_id)
    {
        $mesa = Mesa::find($mesa_id);

        if (!$mesa) {
            return response()->json(['message' => 'Mesa no encontrada'], 404);
        }

        return response()->json($mesa, 200);
    }

    /**
     * Actualizar una mesa.
     */
    public function update(Request $request, $mesa_id)
    {
        // Buscar la mesa por su 'mesa_id'
        $mesa = Mesa::find($mesa_id);
    
        // Verificar si la mesa existe
        if (!$mesa) {
            return response()->json(['message' => 'Mesa no encontrada'], 404);
        }
    
        // Validar los datos de la mesa
        $request->validate([
            'numero_mesa' => 'integer|unique:mesa,numero_mesa,' . $mesa->mesa_id . ',mesa_id',
            'capacidad' => 'integer|min:1',

            'disponibilidad' => 'boolean',
        ]);
    
        // Actualizar la mesa con los datos proporcionados
        $mesa->update($request->only(['numero_mesa', 'capacidad', 'disponibilidad']));

    
        // Devolver la respuesta con la mesa actualizada
        return response()->json([
            'message' => 'Mesa actualizada exitosamente',
            'mesa' => $mesa
        ], 200);
    }
    
    /**
     * Eliminar una mesa.
     */
    public function destroy($mesa_id)
    {
        $mesa = Mesa::find($mesa_id);

        if (!$mesa) {
            return response()->json(['message' => 'Mesa no encontrada'], 404);
        }

        $mesa->delete();

        return response()->json(['message' => 'Mesa eliminada exitosamente'], 200);
    }

    /**
     * Cambiar la disponibilidad de una mesa.
     */
    public function cambiarDisponibilidad($mesa_id, Request $request)
{
    $mesa = Mesa::find($mesa_id);

    if (!$mesa) {
        return response()->json(['message' => 'Mesa no encontrada'], 404);
    }

    $disponibilidad = $request->input('disponibilidad'); // Obtén el valor del cuerpo de la solicitud
    $mesa->disponibilidad = $disponibilidad;
    $mesa->save();

    return response()->json(['message' => 'Disponibilidad de la mesa actualizada exitosamente'], 200);
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