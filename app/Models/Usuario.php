<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject; // Importa la interfaz
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Usuario extends Authenticatable implements JWTSubject // Implementa la interfaz
{
    use HasFactory;

    protected $primaryKey = 'usuario_id';
    protected $table = 'usuarios';
    protected $fillable = ['username', 'password', 'email', 'role_id', 'restaurante_id'];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function restaurante()
    {
        return $this->belongsTo(Restaurante::class, 'restaurante_id');
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'usuario_id');
    }
    public function compra()
    {
        return $this->hasMany(Compra::class, 'usuario_id');
    }
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Devuelve la clave primaria del usuario
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return []; // Puedes agregar claims personalizados aquí si lo necesitas
    }
}