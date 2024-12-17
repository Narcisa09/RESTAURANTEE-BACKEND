<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disponibilidad extends Model
{
    use HasFactory;

    protected $primaryKey = 'disponibilidad_id';
    protected $table = 'disponibilidad';
    protected $fillable = ['mesa_id', 'fecha_disponible', 'hora_inicio', 'hora_fin', 'estado_disponibilidad'];

    public function mesa()
    {
        return $this->belongsTo(Mesa::class, 'mesa_id');
    }
}