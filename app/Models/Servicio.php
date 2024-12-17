<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    use HasFactory;

    protected $primaryKey = 'servicio_id'; // Clave primaria
    protected $fillable = ['costo']; // Campos asignables en masa

    /**
     * Relación con compras
     */
    public function compras()
    {
        return $this->hasMany(Compra::class, 'servicio_id');
    }
}
