<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proyecto extends Model
{
    use HasFactory;

    protected $table = 'proyectos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_hora',
        'codigo_id',
        'estatus',
        'user_id',
    ];

    public function integrantes()
    {
        return $this->belongsToMany(\App\Models\User::class, 'proyecto_usuario', 'proyecto_id', 'usuario_id');
    }

    public function codigo()
    {
        return $this->belongsTo(\App\Models\Codigo::class, 'codigo_id');
    }

    public function imagenes()
    {
        return $this->hasMany(ProyectoImagen::class, 'proyecto_id');
    }

    public function creador()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
