<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarjetaCredito extends Model
{
    use HasFactory;

    protected $primaryKey = 'tarjeta_id'; // Clave primaria
    protected $fillable = ['numero_tarjeta','fecha_expiracion','cvc','saldo']; // Campos asignables en masa

    /**
     * Relación con compras
     */
    public function compra()
    {
        return $this->hasMany(Compra::class, 'tarjeta_id');
    }
}
