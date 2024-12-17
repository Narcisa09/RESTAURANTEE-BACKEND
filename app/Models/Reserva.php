<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $primaryKey = 'reserva_id';
    protected $table ='reserva';  // Nombre de la tabla en la BD, si es diferente al nombre del modelo en CamelCase
    protected $fillable = ['usuario_id', 'mesa_id', 'fecha_reserva', 'estado'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id'); // Referencia a Usuario
    }

    public function mesa()
    {
        return $this->belongsTo(Mesa::class, 'mesa_id');
    }
}