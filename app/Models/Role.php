<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $primaryKey = 'role_id'; 
    protected $fillable = ['role_name']; 
    public $timestamps = false; 

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'role_id'); // Referencia a Usuario
    }

    public function permisos()
    {
        return $this->hasMany(Permiso::class, 'role_id');
    }
}

