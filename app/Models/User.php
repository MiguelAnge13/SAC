<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'division',
        'correo',
        'password',
        'tipo'
    ];

    protected $hidden = ['password', 'remember_token'];

    public function esAdministrador(): bool
    {
        return $this->tipo === 'administrador';
    }
}
