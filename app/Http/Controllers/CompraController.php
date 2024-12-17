<?php
namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Servicio;
use App\Models\TarjetaCredito;
use App\Models\Usuario;
use App\Models\Role;
use App\Models\Restaurante; // Importar el modelo Restaurante
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class CompraController extends Controller
{
    public function store(Request $request)
    {
        // Validación de los datos de entrada
        $validator = Validator::make($request->all(), [
            'numero_tarjeta' => 'required|string|exists:tarjeta_creditos,numero_tarjeta',
            'fecha_expiracion' => 'required|date_format:Y-m-d',
            'cvc' => 'required|string|digits:3',
            'servicio_id' => 'required|exists:servicios,servicio_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Obtener el usuario autenticado
        $usuario = JWTAuth::parseToken()->authenticate();

        // Verificar si el usuario tiene el rol 1
        if ($usuario->role_id !== 1) {
            return response()->json(['error' => 'Acceso denegado: solo el usuario con rol 1 puede realizar esta compra.'], 403);
        }

        // Buscar la tarjeta y el servicio
        $tarjeta = TarjetaCredito::where('numero_tarjeta', $request->numero_tarjeta)->first();
        $servicio = Servicio::findOrFail($request->servicio_id);

        // Verificar si el saldo de la tarjeta es suficiente
        if ($tarjeta->saldo >= $servicio->costo) {
            // Realizar la compra
            $compra = new Compra();
            $compra->usuario_id = $usuario->usuario_id;
            $compra->tarjeta_id = $tarjeta->tarjeta_id;
            $compra->monto = $servicio->costo;
            $compra->fecha_compra = now();
            $compra->save();

            // Crear un nuevo restaurante
            $restaurante = Restaurante::create([
                'nombre_restaurante' => 'Restaurante de ' . $usuario->username, // Nombre genérico basado en el usuario
                'direccion' => null,
                'telefono' => null,
                'email' => null,
                'descripcion' => null,
            ]);
            
            $rolAdmin = Role::find(2);
            if (!$rolAdmin) {
                return response()->json(['error' => 'El rol de administrador no existe en la base de datos.'], 400);
            }

            // Asignar el rol al usuario
            $usuario->role_id = $rolAdmin->role_id;
            $usuario->save();

            // Asignar el restaurante al usuario
            $usuario->restaurante_id = $restaurante->restaurante_id;
            $usuario->save();


            return response()->json([
                'message' => 'Compra realizada con éxito',
                'compra' => $compra,
                'restaurante' => $restaurante
            ], 201);
        } else {
            return response()->json(['message' => 'Saldo insuficiente'], 400);
        }
    }
}