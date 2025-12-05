<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Libreria extends Model
{
    use HasFactory;

    protected $table = 'librerias';

    protected $fillable = [
        'nombre',
        'lenguaje',
        'version',
        'descripcion',
        'icono',
        'user_id'
    ];

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // helper para obtener URL pública del icono
    public function iconoUrl()
    {
        return $this->icono ? asset('storage/' . $this->icono) : asset('logo.png');
    }
}
