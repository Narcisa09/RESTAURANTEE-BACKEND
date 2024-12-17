<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $primaryKey = 'compra_id'; // Clave primaria
    protected $table = 'compra';
    protected $fillable = ['usuario_id', 'tarjeta_id', 'monto', 'fecha_compra']; // Campos asignables en masa

    /**
     * Relación con el usuario que realizó la compra
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Relación con la tarjeta de crédito utilizada
     */
    public function tarjeta()
    {
        return $this->belongsTo(TarjetaCredito::class, 'tarjeta_id');
    }

    /**
     * Relación con el servicio comprado
     */
    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }
}
