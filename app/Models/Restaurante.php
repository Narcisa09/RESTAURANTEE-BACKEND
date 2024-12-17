<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurante extends Model
{
    use HasFactory;

    protected $primaryKey = 'restaurante_id';
    protected $table = 'restaurantes';
    protected $fillable = ['nombre_restaurante','direccion','telefono','email','descripcion'];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'restaurante_id'); // Referencia a Usuario
    }

    public function mesas()
    {
        return $this->hasMany(Mesa::class, 'restaurante_id');
    }
}

